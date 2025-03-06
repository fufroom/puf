# 🌱🍄 Puf Framework - Nginx Setup Guide 🌿

This guide configures **Nginx** for **Puf Framework** with a secure structure:

```
/var/www/example.com/
├── serve/   # 🌱 Public files (served by Nginx)
├── private/ # 🍄 Private files (blocked by Nginx)
└── upload.sh # 🚀 Deployment script
```

---

## **🍃 Nginx Configuration**
```nginx
server {
    server_name example.com www.example.com;
    root /var/www/example.com/serve;
    index index.php index.html;

    location / { try_files $uri $uri/ /index.php?$query_string; }
    location /private/ { deny all; return 403; }
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
    location ~ /\. { deny all; return 403; }

    listen 443 ssl;
    ssl_certificate /etc/letsencrypt/live/example.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/example.com/privkey.pem;
    include /etc/letsencrypt/options-ssl-nginx.conf;
}

server {
    listen 80;
    server_name example.com www.example.com;
    return 301 https://$host$request_uri;
}
```

---

## **🌿 Deployment with `upload.sh`**
Uploads **public files to `serve/`** and **private files to `private/`**.

```bash
#!/bin/bash
SERVER="user@server:/var/www/example.com"

rsync -avz --delete project/ $SERVER/serve/
rsync -avz --delete private/ $SERVER/private/
ssh $SERVER "sudo systemctl restart nginx"

echo "🌱 Deployment complete!"
```

---

## **🍄 Security Features**
✅ Blocks `private/` from web access  
✅ Redirects HTTP to HTTPS  
✅ Prevents access to `.git`, `.env`, and hidden files  

---

## **🚀 Quick Start**
1️⃣ **Set up Nginx** (`/etc/nginx/sites-available/example.com`)  
2️⃣ **Enable & restart**:
```bash
sudo ln -s /etc/nginx/sites-available/example.com /etc/nginx/sites-enabled/
sudo systemctl restart nginx
```
3️⃣ **Deploy**:
```bash
./upload.sh
```
4️⃣ **Visit** 👉 `https://example.com`

This keeps your **Puf project contained, secure, and easy to deploy.** 🌱🍄🚀
