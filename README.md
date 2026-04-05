# ☁️ Cloud-Based 3-Tier Expense Tracker (AWS Deployment)

A fully deployed **3-Tier Cloud Application on AWS** demonstrating real-world production architecture using:

- Auto Scaling  
- Reverse Proxy (Nginx)  
- Private Networking (VPC)  
- SSH Jump Host (Bastion via Web Server)  
- Database Isolation  

This project simulates how real SaaS applications are deployed in production cloud environments.

---

# 🚀 Live Features

- Add expenses  
- View expenses  
- Delete expenses  
- Real-time total calculation  
- Fully deployed on AWS infrastructure  

---

# 🏗️ AWS Network Architecture

## 🌐 1️⃣ VPC Creation

We created a custom VPC using a large private IP range.

| Component | Value |
|---|---|
| **VPC CIDR** | `10.0.0.0/16` |
| Total IPs | 65,536 |

This CIDR allows creation of multiple subnets for a 3-tier architecture.

---

## 🧩 2️⃣ Subnet Design (3-Tier)

| Tier | Subnet | CIDR | Purpose |
|---|---|---|---|
| Public Tier | Public Subnet | `10.0.1.0/24` | Internet facing Web Server |
| App Tier | Private App Subnet | `10.0.2.0/24` | Backend API Server |
| Data Tier | Private DB Subnet | `10.0.3.0/24` | MariaDB Database |

---

## 📷 Architecture Diagram

<img width="1299" height="1210" src="https://github.com/user-attachments/assets/86e3cdd8-6b19-4d98-94c2-0e2978f42bfd" />

---

# 🔐 Secure Access (SSH Jump Host)

Private servers **cannot be accessed directly from internet**.  
We use the **Web Server as Bastion Host**.

### Copy Private Keys to Web Server

Run from your local machine:

```bash
scp -i path/location.pem keyname.pem ubuntu@WEB_PUBLIC_IP:~
```

### SSH into Web Server

```bash
ssh -i location.pem ubuntu@WEB_PUBLIC_IP
```

### SSH into App Server from Web Server

```bash
ssh -i keyname.pem ubuntu@APP_PRIVATE_IP
```

### SSH into DB Server from Web Server

```bash
ssh -i keyname.pem ubuntu@DB_PRIVATE_IP
```

This simulates **real production security**.

---

# 🧠 Infrastructure Design

| Layer | Service | Subnet |
|---|---|---|
| Web Tier | Nginx Reverse Proxy + Frontend | Public Subnet |
| App Tier | Nginx + PHP REST API | Private Subnet |
| DB Tier | MariaDB Database | Private Subnet |

---

# 🔄 Request Flow

User → Internet → Web Server (Auto Scaling)  
↓ Reverse Proxy  
App Server (Elastic IP used internally)  
↓  
DB Server  

---

# 🚀 High Availability

- Web Servers in **Auto Scaling Group**
- Reverse Proxy → internal routing
- Elastic IP on App Server for stable communication

## 📷 Auto Scaling Screenshot

<img width="1919" height="858" src="https://github.com/user-attachments/assets/da43c5ac-fd23-4f00-85cb-1df811112bd1" />

---

# 💻 Tech Stack

| Layer | Technology |
|---|---|
| Frontend | HTML, CSS, JavaScript |
| Backend | PHP |
| Web Server | Nginx |
| Database | MariaDB |
| Cloud | AWS EC2, VPC, ASG |

---

## 📷 Application Screenshot

<img width="1919" height="868" src="https://github.com/user-attachments/assets/63b8e912-b27b-455d-9ef3-1b491b10b259" />

---

# ⚙️ Deployment Steps

---

# 🟢 Web Server Setup (Public EC2)

```bash
sudo apt update
sudo apt install nginx -y
sudo nano /var/www/html/index.html
```

### Reverse Proxy Setup
```bash
sudo nano /etc/nginx/sites-enabled/default
```

```nginx
location /api {
    proxy_pass http://APP_PRIVATE_IP;
}
```

```bash
sudo systemctl restart nginx
sudo ufw allow 80
```

---

# 🟡 App Server Setup (Private EC2)

```bash
sudo apt update
sudo apt install nginx mariadb-server php php8.3-fpm php8.3-mysql -y
sudo apt install php8.3-cli php8.3-common php8.3-curl php8.3-mbstring -y
sudo nano /var/www/html/api.php
```

Enable DB connections:

```bash
sudo nano /etc/mysql/mariadb.conf.d/50-server.cnf
```

Change:
```ini
bind-address = 0.0.0.0
```

```bash
sudo service mariadb restart
sudo systemctl restart nginx
sudo systemctl restart php8.3-fpm
```

---

# 🔵 Database Server Setup (Private EC2)

```bash
sudo mysql
```

```sql
CREATE DATABASE expense_app;
USE expense_app;

CREATE TABLE expenses(
 id INT AUTO_INCREMENT PRIMARY KEY,
 title VARCHAR(100),
 amount DECIMAL(10,2),
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

```bash
sudo service mariadb restart
```

---

# 🧪 API Test

```bash
curl -X POST -H "Content-Type: application/json" \
-d '{"title":"Tea","amount":10}' \
"http://APP_ELASTIC_IP/api.php?action=add"
```

---
---

# 🌐 Live Application

🚀 **Application URL**

http://YOUR_WEB_SERVER_PUBLIC_IP

> This application is deployed on AWS using Auto Scaling and a 3-Tier Architecture.


# 📌 Key Learning Outcomes

- AWS VPC & Subnet Architecture  
- Bastion Host (SSH Jump Server)  
- Reverse Proxy (Nginx)  
- Auto Scaling Deployment  
- Private Networking Security  

---

# 👩‍💻 Author

**Pratiksha Lavand**

---

⭐ Give this repo a star if you like it!
