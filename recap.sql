Step 1 — origin/main အထိ soft reset ပါ (ဒါက code ကို မပျက်စီးပါ၊ commit history ကိုပဲ ပြန်ပြန်ဖန်တီးမှာပါ):
bashgit reset --soft 9df6615
Step 2 — Status ကို စစ်ပါ:
bashgit status
သင့် mail system အပြောင်းအလဲ အကုန် staged အနေနဲ့ ပြနေသင့်ပါတယ် (test.md ပါမှာ မဟုတ်ပါဘူး၊ ဖျက်ပြီးသားမို့)။
Step 3 — test.md ဟန်တီးထားလား နောက်တစ်ကြိမ် confirm ပါ:
bashls test.md
ဖိုင် ရှိနေသေးရင် (No such file မပြရင်) ဖျက်ပါ:
bashgit rm --cached test.md 2>/dev/null
rm -f test.md
Step 4 — Commit အသစ်တစ်ခု ပြန်ဖန်တီးပါ:
bashgit add .
git commit -m "Add mail system with Brevo SMTP integration"
Step 5 — Push ပြန်လုပ်ပါ:
bashgit push origin main
ဒီတစ်ကြိမ် history ထဲမှာ secret ပါတဲ့ commit လုံးဝ ရှိမနေတော့ပါဘူး — GitHub push protection ကျော်သွားသင့်ပါတယ်။



here is database ,we have very important process , i want how many job currently procressing on server . in this photo every things is success and error that's mean finished . we can know status by name (processing)   is currently running on server but maybe it's can stuck so we can check with time how long it take if process is longer than 30min it's bug error but i havent' face like that i just tell you to know . here is database work at server=> 

def save_job(job_id: str, user_id: int, server_type: str = "normal"):
    conn = get_db()
    with conn.cursor() as cur:
        cur.execute("""
            INSERT INTO recap_jobs 
                (id, user_id, status, step, progress, server, created_at, updated_at)
            VALUES (%s, %s, 'processing', 1, 0, %s, NOW(), NOW())
        """, (job_id, user_id, server_type))
    conn.commit()
    conn.close()

if finish or error it's show success and error. here is the main part i want to do.i call from vue to know currently with server is busy ( processing ) with their server name, so i can see which server is currently busy and i can move to another server. 

      servers: {
        v1: 'https://zakerxa-zakerxa-v1.hf.space',
        v2: 'https://zakerxav2-zakerxav2-v2.hf.space',
        v3: 'https://zakerxav3-zakerxav3-v3.hf.space',
        recap: 'https://recap.zakerxa.com',
      },

here is old vue code to check server down or not , firstly we check all server is active ? 


    // Single endpoint health check with timeout
    async checkHealth(url, timeoutMs = 4000) {
      const controller = new AbortController();
      const timer = setTimeout(() => controller.abort(), timeoutMs);
      try {
        const res = await fetch(`${url}/health`, { signal: controller.signal });
        clearTimeout(timer);
        if (!res.ok) return false;

        const data = await res.json();
        console.log(`[health] ${url} ->`, data.status, data.message);

        // TODO (later): queue/semaphore full check
        // if (data.queue_count >= 3) return false;

        return data.status === 'ok' || data.status === 'healthy';
      } catch (err) {
        clearTimeout(timer);
        console.warn(`[health] ${url} unreachable:`, err.message);
        return false;
      }
    },

    // Decide [primary, fallback] pair based on role
    getServerPairForRole(roleName) {
      switch (roleName) {
        case 'tester':
          return [this.servers.v1, this.servers.v3];
        case 'normal':
          return [this.servers.v2, this.servers.v3];
        case 'pro':
          return [this.servers.recap, this.servers.v3];
        default:
          return [this.servers.recap, this.servers.v3];
      }
    },

    // Resolve baseUrl: try primary, fallback if primary fails
    async resolveBaseUrl() {
      const roleName = this.auth.user?.role_name;
      const [primary, fallback] = this.getServerPairForRole(roleName);

      const primaryOk = await this.checkHealth(primary);
      if (primaryOk) {
        this.baseUrl = primary;
        this.baseUrlReady = true;
        // console.log(`[resolveBaseUrl] role=${roleName} -> primary (${primary})`);
        return primary;
      }

      const fallbackOk = await this.checkHealth(fallback);
      if (fallbackOk) {
        this.baseUrl = fallback;
        this.baseUrlReady = true;
        // console.log(`[resolveBaseUrl] role=${roleName} -> fallback (${fallback})`);
        return fallback;
      }

      // အကုန် fail ရင် primary ကိုပဲ ပြန်ပေးမယ် (startProcess() ထဲက try/catch က handle လုပ်ပေးလိမ့်မယ်)
      // console.error(`[resolveBaseUrl] role=${roleName} -> both primary & fallback failed, defaulting to primary`);
      this.baseUrl = primary;
      this.baseUrlReady = true;
      return primary;
    },

and then we will check busy server among them if server is busy we will switch to another also show user to switching server . explain as burmese