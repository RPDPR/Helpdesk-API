# HELPDESK API

---

## Quick start

git clone https://github.com/RPDPR/Helpdesk-API

cd Helpdesk-API

create a `.env` file in project's root
fill it with `.env.example` contents

docker compose up -d --build

php artisan migrate --seed

php artisan queue:work
