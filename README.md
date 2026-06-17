Markdown
# TikTok Content Automation & Synchronization Pipeline (Zakerxa Bot)

An automated content generation system that uses local AI engines (Gemini) to generate viral TikTok scripts, descriptions, and 9:16 aspect ratio cover art layouts, and seamlessly synchronizes them to a live production Laravel application over a secure API.

---

## 🚀 System Architecture

```text
[ Local Ubuntu Machine ]
   │
   ├── 1. main.sh (Orchestrator)
   ├── 2. daily_tiktok_engine.py (Generates Voiceovers, Hashtags & Image Prompts via Gemini API)
   ├── 3. gemini_operator.py (Automates Image Downloader via Selenium WebDriver)
   └── 4. cURL Multipart Upload ───► [ Live Production API (zakerxa.com) ]
                                                │
                                                ├── Laravel 12 Backend (Saves Post & Stores Media)
                                                └── Inertia.js + Vue 3 Frontend (Responsive View)
🛠️ Features
Automated Content Generation: Generates multi-topic scripts (Space, Psychology, Productivity, etc.) along with B-roll visual blueprints and hashtags.

Dynamic Image Prompts: Automatically crafts detailed prompts and grabs customized cover images.

Automated Production Sync: Uses a secure token-protected API endpoint via cURL to transfer localized data and physical file assets instantly to https://zakerxa.com.

Responsive Layout: Front-end built with Vue 3 and Tailwind CSS featuring a dynamic grid layout that automatically adjusts to a structured column sequence (Title -> Tags -> Image -> Content) on mobile devices.

💻 1. Local Engine Setup (Ubuntu Linux)
Directory Structure
Ensure your directory layout on the local system looks exactly like this:

Plaintext
/path/path/path/TikTok/
├── env/                   # Python Virtual Environment
├── Database/
│   └── data.json          # Main Local JSON Storage File
├── logs/
│   ├── track.txt          # Runtime Operations Tracker Log
│   └── error_output.log   # System Standard Error Log
├── images/                # Temporary Download Directory for Images
├── main.sh                # Main Automation Orchestrator Script
├── daily_tiktok_engine.py # Core Python script talking to Gemini API
└── gemini_operator.py     # Selenium UI Automated downloader


Setup Prerequisites
Initialize Virtual Environment & Install Dependencies:

Bash
cd /path/path/path/TikTok
python3 -m venv env
source env/bin/activate
pip install google-generativeai requests python-dotenv selenium
Install CLI Utilities:
Ensure jq is installed on your Ubuntu system for managing data piping:

Bash
sudo apt update
sudo apt install jq
Configure Environment Variables (.env):
Create a .env file inside /path/path/path/TikTok/:

ကုဒ်စာသား
GEMINI_API_KEY=your_actual_gemini_api_key_here
Make the Runner Executable:

Bash
chmod +x main.sh
🌐 2. Production Web Server Setup (Laravel 12 + Vue 3)
API Endpoint Initialization
Laravel 11 comes minimal by default. If the API layer is not installed, trigger it inside your remote server terminal:

Bash
php artisan install:api
Route Registration (routes/api.php)
PHP
use App\Http\Controllers\Api\TikTokPostController;

Route::post('/posts/upload', [TikTokPostController::class, 'upload']);
Shared Hosting Storage Symlink (cPanel)
If your application core is separated from your root web directory (public_html), create the symbolic link manually to expose asset files to the browser framework:

Bash
ln -s /home/your_cpanel_username/laravel_project/storage/app/public /home/your_cpanel_username/public_html/storage
🏃‍♂️ How to Run the Pipeline
Simply run the master orchestrator script. It will run the generation matrix, output the database elements, fetch assets, and sync them immediately to production:

Bash
./main.sh