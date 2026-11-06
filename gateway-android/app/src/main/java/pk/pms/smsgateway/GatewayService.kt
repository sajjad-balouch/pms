package pk.pms.smsgateway

import android.app.Activity
import android.app.Notification
import android.app.NotificationChannel
import android.app.NotificationManager
import android.app.PendingIntent
import android.app.Service
import android.content.BroadcastReceiver
import android.content.Context
import android.content.Intent
import android.content.IntentFilter
import android.content.pm.ServiceInfo
import android.os.BatteryManager
import android.os.Build
import android.os.IBinder
import android.os.PowerManager
import android.telephony.SmsManager
import org.json.JSONArray
import org.json.JSONObject
import java.util.concurrent.CountDownLatch
import java.util.concurrent.TimeUnit
import java.util.concurrent.atomic.AtomicReference

/**
 * Background mein chalta rehta hai:
 *   server se agle SMS lo -> SIM se bhejo -> server ko batao (sent / failed) -> thora ruko -> dobara
 */
class GatewayService : Service() {

    @Volatile private var running = false
    private var worker: Thread? = null
    private var wakeLock: PowerManager.WakeLock? = null

    /** Server tak na pohanchne wali reports; agli dafa pehle yeh bheji jati hain (warna SMS dobara ja sakta hai) */
    private val pendingReports = JSONArray()

    override fun onBind(intent: Intent?): IBinder? = null

    override fun onStartCommand(intent: Intent?, flags: Int, startId: Int): Int {
        if (intent?.action == ACTION_STOP) {
            Prefs(this).enabled = false
            stopSelf()
            return START_NOT_STICKY
        }

        startForegroundCompat("Shuru ho raha hai…")
        if (!running) {
            running = true
            wakeLock = (getSystemService(POWER_SERVICE) as PowerManager)
                .newWakeLock(PowerManager.PARTIAL_WAKE_LOCK, "pms:gateway").apply { acquire() }
            worker = Thread({ loop() }, "gateway").apply { start() }
        }
        return START_STICKY
    }

    override fun onDestroy() {
        running = false
        worker?.interrupt()
        wakeLock?.takeIf { it.isHeld }?.release()
        Prefs(this).status = "Band"
        super.onDestroy()
    }

    private fun loop() {
        val prefs = Prefs(this)
        while (running) {
            var waitSeconds = 20
            try {
                if (prefs.url.isBlank() || prefs.token.isBlank()) {
                    setStatus("Server URL / token nahi")
                } else {
                    val api = GatewayApi(prefs.url, prefs.token)
                    flushReports(api)

                    val res = api.pull(battery())
                    waitSeconds = res.optInt("poll_seconds", 20).coerceIn(5, 300)
                    val delayMs = res.optLong("delay_ms", 3000).coerceIn(0, 60_000)
                    val messages = res.optJSONArray("messages") ?: JSONArray()

                    for (i in 0 until messages.length()) {
                        if (!running) break
                        val m = messages.getJSONObject(i)
                        setStatus("Bhej raha hai: ${m.getString("to")}")
                        val error = sendSms(m.getString("to"), m.getString("body"))

                        if (error == null) prefs.sentToday = prefs.sentToday + 1 else prefs.failedToday = prefs.failedToday + 1
                        synchronized(pendingReports) {
                            pendingReports.put(JSONObject().put("id", m.getLong("id")).put("status", if (error == null) "sent" else "failed").apply {
                                if (error != null) put("error", error)
                            })
                        }
                        flushReports(api)
                        if (i < messages.length() - 1) Thread.sleep(delayMs)
                    }

                    setStatus(if (res.optBoolean("active", true)) "Chalu — aaj ${prefs.sentToday} gaye, ${prefs.failedToday} nakaam" else "Admin ne yeh phone band kiya hua hai")
                    // Queue mein aur bhi hon to foran dobara
                    if (messages.length() > 0) waitSeconds = (delayMs / 1000).toInt().coerceAtLeast(1)
                }
            } catch (e: InterruptedException) {
                break
            } catch (e: Exception) {
                setStatus("Server se rabta nahi: ${e.message ?: e.javaClass.simpleName}")
            }

            try {
                Thread.sleep(waitSeconds * 1000L)
            } catch (e: InterruptedException) {
                break
            }
        }
    }

    private fun flushReports(api: GatewayApi) {
        val batch = synchronized(pendingReports) {
            if (pendingReports.length() == 0) return
            JSONArray(pendingReports.toString())
        }
        try {
            api.report(batch)
            synchronized(pendingReports) {
                // Sirf bheji hui reports nikalo (is dauran nayi aa gayi hon to rehne do)
                repeat(batch.length()) { pendingReports.remove(0) }
            }
        } catch (e: Exception) {
            setStatus("Report baad mein jayegi: ${e.message}")
        }
    }

    /** @return null = kamyab, warna wajah */
    private fun sendSms(to: String, body: String): String? {
        val sms: SmsManager = if (Build.VERSION.SDK_INT >= 31) getSystemService(SmsManager::class.java) else @Suppress("DEPRECATION") SmsManager.getDefault()
        val parts = sms.divideMessage(body)
        val action = "$packageName.SENT.${System.nanoTime()}"
        val latch = CountDownLatch(parts.size)
        val error = AtomicReference<String?>(null)

        val receiver = object : BroadcastReceiver() {
            override fun onReceive(c: Context, i: Intent) {
                if (resultCode != Activity.RESULT_OK) error.compareAndSet(null, errorName(resultCode))
                latch.countDown()
            }
        }
        if (Build.VERSION.SDK_INT >= 33) {
            registerReceiver(receiver, IntentFilter(action), Context.RECEIVER_NOT_EXPORTED)
        } else {
            @Suppress("UnspecifiedRegisterReceiverFlag")
            registerReceiver(receiver, IntentFilter(action))
        }

        try {
            val sent = ArrayList<PendingIntent>()
            for (k in parts.indices) {
                sent.add(PendingIntent.getBroadcast(this, k, Intent(action).setPackage(packageName), PendingIntent.FLAG_IMMUTABLE or PendingIntent.FLAG_UPDATE_CURRENT))
            }
            if (parts.size == 1) sms.sendTextMessage(to, null, body, sent[0], null)
            else sms.sendMultipartTextMessage(to, null, parts, sent, null)

            if (!latch.await(90, TimeUnit.SECONDS)) error.compareAndSet(null, "Network ka jawab nahi aaya")
        } catch (e: SecurityException) {
            error.compareAndSet(null, "SMS ki ijazat nahi")
        } catch (e: Exception) {
            error.compareAndSet(null, e.message ?: "SMS error")
        } finally {
            runCatching { unregisterReceiver(receiver) }
        }
        return error.get()
    }

    private fun errorName(code: Int) = when (code) {
        SmsManager.RESULT_ERROR_GENERIC_FAILURE -> "Generic failure (balance / SMS package khatam?)"
        SmsManager.RESULT_ERROR_NO_SERVICE -> "Signal nahi"
        SmsManager.RESULT_ERROR_NULL_PDU -> "Null PDU"
        SmsManager.RESULT_ERROR_RADIO_OFF -> "Radio band (airplane mode?)"
        else -> "SMS error $code"
    }

    private fun battery(): Int? = runCatching {
        (getSystemService(BATTERY_SERVICE) as BatteryManager).getIntProperty(BatteryManager.BATTERY_PROPERTY_CAPACITY)
    }.getOrNull()?.takeIf { it in 0..100 }

    private fun setStatus(text: String) {
        Prefs(this).status = text
        (getSystemService(NOTIFICATION_SERVICE) as NotificationManager).notify(NOTIFICATION_ID, notification(text))
    }

    private fun notification(text: String): Notification {
        val nm = getSystemService(NOTIFICATION_SERVICE) as NotificationManager
        if (Build.VERSION.SDK_INT >= 26 && nm.getNotificationChannel(CHANNEL) == null) {
            nm.createNotificationChannel(NotificationChannel(CHANNEL, "SMS Gateway", NotificationManager.IMPORTANCE_LOW))
        }
        val open = PendingIntent.getActivity(this, 0, Intent(this, MainActivity::class.java), PendingIntent.FLAG_IMMUTABLE)
        val builder = if (Build.VERSION.SDK_INT >= 26) Notification.Builder(this, CHANNEL) else @Suppress("DEPRECATION") Notification.Builder(this)
        return builder
            .setSmallIcon(android.R.drawable.sym_action_email)
            .setContentTitle("PMS SMS Gateway")
            .setContentText(text)
            .setContentIntent(open)
            .setOngoing(true)
            .build()
    }

    private fun startForegroundCompat(text: String) {
        val n = notification(text)
        if (Build.VERSION.SDK_INT >= 34) startForeground(NOTIFICATION_ID, n, ServiceInfo.FOREGROUND_SERVICE_TYPE_SPECIAL_USE)
        else startForeground(NOTIFICATION_ID, n)
    }

    companion object {
        const val ACTION_STOP = "pk.pms.smsgateway.STOP"
        private const val CHANNEL = "gateway"
        private const val NOTIFICATION_ID = 1

        fun start(context: Context) {
            val i = Intent(context, GatewayService::class.java)
            if (Build.VERSION.SDK_INT >= 26) context.startForegroundService(i) else context.startService(i)
        }
    }
}
