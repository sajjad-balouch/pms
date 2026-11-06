package pk.pms.smsgateway

import android.content.Context
import org.json.JSONArray
import org.json.JSONObject
import java.net.HttpURLConnection
import java.net.URL

/** App ki settings (server URL, token) aur aakhri haal */
class Prefs(context: Context) {
    private val p = context.getSharedPreferences("gateway", Context.MODE_PRIVATE)

    var url: String
        get() = p.getString("url", "") ?: ""
        set(v) = p.edit().putString("url", v.trim().trimEnd('/')).apply()

    var token: String
        get() = p.getString("token", "") ?: ""
        set(v) = p.edit().putString("token", v.trim()).apply()

    /** Phone restart ho to service khud chal pare */
    var enabled: Boolean
        get() = p.getBoolean("enabled", false)
        set(v) = p.edit().putBoolean("enabled", v).apply()

    var status: String
        get() = p.getString("status", "Band") ?: "Band"
        set(v) = p.edit().putString("status", v).apply()

    var sentToday: Int
        get() = if (p.getString("day", "") == today()) p.getInt("sent", 0) else 0
        set(v) = p.edit().putString("day", today()).putInt("sent", v).apply()

    var failedToday: Int
        get() = if (p.getString("day", "") == today()) p.getInt("failed", 0) else 0
        set(v) = p.edit().putString("day", today()).putInt("failed", v).apply()

    private fun today() = java.text.SimpleDateFormat("yyyy-MM-dd", java.util.Locale.US).format(java.util.Date())
}

/** PMS server ki gateway API (POST /pull, POST /report) */
class GatewayApi(private val baseUrl: String, private val token: String) {

    fun pull(battery: Int?, max: Int = 10): JSONObject {
        val body = JSONObject().put("max", max).put("app_version", "1.0")
        if (battery != null) body.put("battery", battery)
        return post("/pull", body)
    }

    fun report(results: JSONArray): JSONObject = post("/report", JSONObject().put("results", results))

    private fun post(path: String, body: JSONObject): JSONObject {
        val conn = URL(baseUrl + path).openConnection() as HttpURLConnection
        try {
            conn.requestMethod = "POST"
            conn.connectTimeout = 15_000
            conn.readTimeout = 20_000
            conn.doOutput = true
            conn.setRequestProperty("Content-Type", "application/json")
            conn.setRequestProperty("Accept", "application/json")
            conn.setRequestProperty("Authorization", "Bearer $token")
            conn.outputStream.use { it.write(body.toString().toByteArray()) }

            val code = conn.responseCode
            val text = (if (code in 200..299) conn.inputStream else conn.errorStream)?.bufferedReader()?.use { it.readText() } ?: ""
            if (code !in 200..299) {
                val msg = runCatching { JSONObject(text).optString("message") }.getOrNull()
                throw RuntimeException("HTTP $code ${msg ?: text.take(120)}")
            }
            return JSONObject(text)
        } finally {
            conn.disconnect()
        }
    }
}
