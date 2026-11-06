# PMS SMS Gateway (Android)

Admin ka apna SMS gateway. SIM aur phone admin ke paas rehte hain. Users (town owners) jab paigham bhejte hain to PMS un ke **wallet** se paise kaat kar paigham queue mein daal deta hai. Ye app har ~20 second baad queue check karti hai aur SIM se SMS bhej deti hai. SMS na jaye to user ko paise khud wapas mil jate hain.

```
User (PMS) ──> Messenger ──> wallet se Rs. kate ──> message_logs (queued)
                                                         │
Android phone  <── POST /api/gateway/pull  ──────────────┘
      │ SIM se SMS
      └──> POST /api/gateway/report (sent / failed → failed par paise wapas)
```

## 1. Server (PMS) par

1. Migration chalayein:

   ```bash
   php artisan migrate
   ```

2. Admin portal > **Messaging** > tareeqa **"Apna SMS gateway"** chunein, Rs. fi SMS likhein, mehfooz karein.
3. Isi page par **Gateway phones** > "+ Phone jorein". Jo token (`gw_…`) dikhe, wo copy kar lein. Ye dobara nahi dikhega.
4. Server URL bhi wahin likha hai (maslan `https://aapki-site.com/api/gateway`).
   - Phone `localhost` nahi khol sakta. Ghar/office mein test karna ho to computer ka WiFi IP use karein: `http://192.168.1.5/pms/api/gateway`.

## 2. App banana (Android Studio)

1. Android Studio mein **File > Open** karke ye `gateway-android` folder kholein. Gradle sync khud ho jayega.
2. Phone USB se lagayein (Developer options > USB debugging on) aur **Run ▶** dabayein.
   APK chahiye ho to **Build > Build APK(s)**, phir `app/build/outputs/apk/debug/app-debug.apk` phone mein daal kar install karein.
3. App mein Server URL aur token likhein, phir **"Mehfooz karke chalu karein"** dabayein aur SMS ki ijazat dein.
4. **"Battery optimization se nikalein"** zaroor dabayein, warna phone app ko so jane par band kar dega.
   Xiaomi, Oppo, Vivo, Infinix waghera par Settings mein app ka **Autostart** bhi on karein.

## Zaroori baatein

- Phone charger par aur WiFi/data on rahe. Phone restart ho to gateway khud chalu ho jata hai.
- SIM par SMS bundle lagwa lein. Har phone ki **rozana had** admin portal mein rakhein (default 300).
  Ek SIM se jaldi jaldi bohat SMS gaye to telecom SIM block kar sakti hai. "Waqfa" setting (default 3 second) isi liye hai.
- Zyada SMS chahiyein to doosra phone/SIM jor dein. Queue khud dono mein bant jati hai.
- Dual-SIM phone mein default SMS wali SIM use hoti hai.
- Ye app sirf aap ke apne server ke paighamat bhejti hai. Bulk marketing/spam ke liye PTA ke qawaneen alag hain.
