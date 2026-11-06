package pk.pms.smsgateway

import android.Manifest
import android.annotation.SuppressLint
import android.app.Activity
import android.content.BroadcastReceiver
import android.content.Context
import android.content.Intent
import android.content.pm.PackageManager
import android.net.Uri
import android.os.Build
import android.os.Bundle
import android.os.Handler
import android.os.Looper
import android.os.PowerManager
import android.provider.Settings
import android.text.InputType
import android.view.ViewGroup.LayoutParams.MATCH_PARENT
import android.view.ViewGroup.LayoutParams.WRAP_CONTENT
import android.widget.Button
import android.widget.EditText
import android.widget.LinearLayout
import android.widget.ScrollView
import android.widget.TextView
import android.widget.Toast

/** Ek hi screen: server URL + token likhein, "Chalu karein" dabayein */
class MainActivity : Activity() {

    private lateinit var prefs: Prefs
    private lateinit var url: EditText
    private lateinit var token: EditText
    private lateinit var status: TextView
    private val handler = Handler(Looper.getMainLooper())
    private val refresh = object : Runnable {
        override fun run() {
            status.text = "Haal: ${prefs.status}\nAaj: ${prefs.sentToday} gaye · ${prefs.failedToday} nakaam"
            handler.postDelayed(this, 2000)
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        prefs = Prefs(this)
        val pad = (16 * resources.displayMetrics.density).toInt()

        val col = LinearLayout(this).apply { orientation = LinearLayout.VERTICAL; setPadding(pad, pad, pad, pad) }
        fun label(t: String) = TextView(this).apply { text = t; setPadding(0, pad, 0, pad / 4) }

        col.addView(label("Server URL (admin portal > Messaging > Gateway phones se)"))
        url = EditText(this).apply {
            hint = "https://aapki-site.com/api/gateway"
            inputType = InputType.TYPE_TEXT_VARIATION_URI
            setText(prefs.url)
        }
        col.addView(url, LinearLayout.LayoutParams(MATCH_PARENT, WRAP_CONTENT))

        col.addView(label("Phone ka token (gw_…)"))
        token = EditText(this).apply {
            hint = "gw_xxxxxxxx"
            inputType = InputType.TYPE_CLASS_TEXT or InputType.TYPE_TEXT_VARIATION_VISIBLE_PASSWORD
            setText(prefs.token)
        }
        col.addView(token, LinearLayout.LayoutParams(MATCH_PARENT, WRAP_CONTENT))

        col.addView(Button(this).apply { text = "Mehfooz karke chalu karein"; setOnClickListener { saveAndStart() } })
        col.addView(Button(this).apply {
            text = "Band karein"
            setOnClickListener {
                prefs.enabled = false
                startService(Intent(this@MainActivity, GatewayService::class.java).setAction(GatewayService.ACTION_STOP))
            }
        })
        col.addView(Button(this).apply { text = "Battery optimization se nikalein"; setOnClickListener { askBatteryExemption() } })

        status = TextView(this).apply { setPadding(0, pad, 0, 0); textSize = 16f }
        col.addView(status)
        col.addView(TextView(this).apply {
            setPadding(0, pad, 0, 0)
            text = "Tips:\n• Phone charger par laga rahe aur WiFi/data on rahe.\n• SIM par SMS package lagwa lein.\n• Ek SIM se rozana chand sau SMS se zyada na bhejein, warna block ho sakti hai."
        })

        setContentView(ScrollView(this).apply { addView(col) })
    }

    override fun onResume() {
        super.onResume()
        handler.post(refresh)
    }

    override fun onPause() {
        handler.removeCallbacks(refresh)
        super.onPause()
    }

    private fun saveAndStart() {
        val u = url.text.toString().trim()
        if (!u.startsWith("http://") && !u.startsWith("https://")) {
            Toast.makeText(this, "URL http:// ya https:// se shuru ho", Toast.LENGTH_LONG).show()
            return
        }
        if (!token.text.toString().trim().startsWith("gw_")) {
            Toast.makeText(this, "Token gw_ se shuru hota hai", Toast.LENGTH_LONG).show()
            return
        }
        prefs.url = u
        prefs.token = token.text.toString()

        val needed = buildList {
            add(Manifest.permission.SEND_SMS)
            if (Build.VERSION.SDK_INT >= 33) add(Manifest.permission.POST_NOTIFICATIONS)
        }.filter { checkSelfPermission(it) != PackageManager.PERMISSION_GRANTED }

        if (needed.isNotEmpty()) requestPermissions(needed.toTypedArray(), 1) else start()
    }

    override fun onRequestPermissionsResult(requestCode: Int, permissions: Array<out String>, grantResults: IntArray) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults)
        if (checkSelfPermission(Manifest.permission.SEND_SMS) == PackageManager.PERMISSION_GRANTED) start()
        else Toast.makeText(this, "SMS ki ijazat ke baghair gateway nahi chal sakta", Toast.LENGTH_LONG).show()
    }

    private fun start() {
        prefs.enabled = true
        GatewayService.start(this)
        Toast.makeText(this, "Gateway chalu", Toast.LENGTH_SHORT).show()
    }

    @SuppressLint("BatteryLife")
    private fun askBatteryExemption() {
        val pm = getSystemService(POWER_SERVICE) as PowerManager
        if (Build.VERSION.SDK_INT >= 23 && !pm.isIgnoringBatteryOptimizations(packageName)) {
            startActivity(Intent(Settings.ACTION_REQUEST_IGNORE_BATTERY_OPTIMIZATIONS, Uri.parse("package:$packageName")))
        } else {
            Toast.makeText(this, "Pehle se nikla hua hai", Toast.LENGTH_SHORT).show()
        }
    }
}

/** Phone restart hone par gateway khud chalu */
class BootReceiver : BroadcastReceiver() {
    override fun onReceive(context: Context, intent: Intent) {
        if (intent.action == Intent.ACTION_BOOT_COMPLETED && Prefs(context).enabled) {
            GatewayService.start(context)
        }
    }
}
