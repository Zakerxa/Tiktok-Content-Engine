  User can see account usage rate or limit. also add time for example if user open account first times with gmail that's must be tester role and only accept to test global 1-times usage forever. but if user increase plan i will set to normal user , but normal user can be accept 3-times per day, after 1days later if user not renew 3times limit is still 0usage because he not renew it. only admin can set this limit when we renew.But pro is different pro also 3times per 1days but after 1days later he will rollback to normal role but usage is still 0.got it ? 

Tester:
  - 1 time forever (global total)
  - ပြီးရင် 0 times — login ဝင်လို့ရ၊ blog သုံးလို့ရ
  - recap feature ပဲ မရတော့

Normal:
  - Admin renew လုပ်မှ limit ရ
  - Buy 11:00 AM → expire (နောက်နေ့ 11:00 AM)
  - 12:00 AM နောက်ဆုံး — 0 times ဖြစ်
  - Renew မလုပ်ရင် 0 times ထားတယ်

Pro:
  - Admin renew လုပ်မှ limit ရ
  - Buy 11:00 AM → expire (နောက်နေ့ 11:00 AM)
  - နောက်နေ့ 11:00 AM နောက်ဆုံး → auto rollback to Normal
  - Normal ရဲ့ rules အတိုင်း ဆက်သုံး but still 0 limit

Admin:
  - Unlimited usage
  - User limit create role edit disable / renew set လုပ်နိုင်


လုပ်ပြီးသားအရာများ ✅
Infrastructure:
  ✅ FastAPI + MySQL (pymysql) ချိတ်ပြီး
  ✅ cPanel Remote MySQL access ဖွင့်ပြီး
  ✅ Cloudflare Tunnel (recap.zakerxa.com)
  ✅ uvicorn --timeout-keep-alive 600
  ✅ global_semaphore(1) — job queue system

Database (cPanel zzcwpszw_tiktok):
  ✅ roles table (tester/normal/pro/vip/admin)
  ✅ users table altered (role_name, plan_expires_at, 
     recap_limit, total_recap_used, api_key, avatar, google_id)
  ✅ usage_log table
  ✅ plan_history table
  ✅ recap_jobs table

CPU Optimization:
  ✅ Tester  → tiny model, beam=1, threads=1, fps=24, bitrate=800k
  ✅ Normal  → small model, beam=2, threads=1, fps=30, bitrate=3M
  ✅ os.nice() priority
  ✅ vad_filter=True

Upload System:
  ✅ Chunk upload (5MB chunks)
  ✅ /upload-chunk route
  ✅ /upload-chunk-finalize route
  ✅ Upload progress UI (📡 Uploading to Server... %)

but we need to move to Vue+laravel route that already previous index.html and main.js


လုပ်ရန်ကျန်သောအရာများ ❌
Database Logic Update:
  ❌ database.py — new schema နဲ့ rewrite
     (email based, plan_expires_at, 
      total_recap_used, pro rollback logic)
  ❌ job_routes.py — recap_jobs table update
     (step/progress/error → MySQL သိမ်း)

Authentication:
  ❌ Google Login integration
  ❌ User profile page (avatar edit, usage rate)

Admin Panel:
  ❌ Renew plan system
  ❌ Set recap_limit per user
  ❌ Pro → Normal auto rollback logic

Frontend (cPanel Laravel/Vue):
  ❌ recap routes merge with Laravel
  ❌ Status polling via recap_jobs table
  ❌ User profile UI

FastAPI → MySQL Status Update:
  ❌ Process လုပ်နေစဉ် recap_jobs table update
  ❌ Server restart ဖြစ်ရင် status recover

Priority အစဉ်အလိုက် Next Steps:
1. database.py rewrite (အရေးအကြီးဆုံး)
   └─ မပြင်ရင် login/session/limit အကုန် မမှန်ဘူး

2. job_routes.py — recap_jobs MySQL update
   └─ status tracking persistent ဖြစ်မယ်

3. Admin panel — renew/limit system
   └─ user management အတွက်

4. Google Login
   └─ tester auto-assign

5. Laravel merge
   └─ cPanel side routes + Vue UI