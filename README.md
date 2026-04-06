# ☁️ Cloud-Based 3-Tier Expense Tracker (AWS Deployment)

<img width="1299" height="1210" alt="Expense tracker app deployment architecture" src="https://github.com/user-attachments/assets/e5699c8c-92ba-4469-8ee0-dc41da5668b1" />

A fully deployed **3-Tier Cloud Application on AWS** demonstrating real-world production architecture using:

- Auto Scaling  
- Reverse Proxy (Nginx)  
- Private Networking (VPC)  
- SSH Jump Host (Bastion)  
- Database Isolation  

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

| Component | Value |
|---|---|
| **VPC CIDR** | `10.0.0.0/16` |
| Total IPs | 65,536 |

---

## 🧩 2️⃣ Subnet Design (3-Tier)

| Tier | Subnet | CIDR | Purpose |
|---|---|---|---|
| Public Tier | Public Subnet | `10.0.1.0/24` | Internet facing Web Server |
| App Tier | Private App Subnet | `10.0.2.0/24` | Backend API Server |
| Data Tier | Private DB Subnet | `10.0.3.0/24` | MariaDB Database |

---

## 📷 Architecture Diagram

<img width="1299" height="1210" alt="Expense tracker app deployment architecture" src="https://github.com/user-attachments/assets/5558fe34-f1f2-4ff3-85bd-d320e0ca0da0" />


---

## 🖥️ EC2 Instances Overview

<img width="1919" height="857" alt="servers" src="https://github.com/user-attachments/assets/f79bb526-fec9-4be2-bfc3-1b54e815c02e" />


👉 Shows:
- Web Server (Public)
- App Server (Private)
- DB Server (Private)

---

# 🔐 Secure Access (SSH Jump Host)

Private servers **cannot be accessed directly from internet**.  
We use the **Web Server as Bastion Host**.

### Copy Private Keys to Web Server

```bash
scp -i path/location.pem keyname.pem ubuntu@WEB_PUBLIC_IP:~
```

### SSH into Web Server

```bash
ssh -i location.pem ubuntu@WEB_PUBLIC_IP
```

### SSH into App Server

```bash
 sudo ssh -i /home/ubuntu/keyname.pem ubuntu@APP_PRIVATE_IP
```

### SSH into DB Server

```bash
 sudo ssh -i /home/ubuntu/keyname.pem ubuntu@DB_PRIVATE_IP
```

---

# 🧠 Infrastructure Design

| Layer | Service | Subnet |
|---|---|---|
| Web Tier | Nginx Reverse Proxy + Frontend | Public Subnet |
| App Tier | Nginx + PHP REST API | Private Subnet |
| DB Tier | MariaDB Database | Private Subnet |

---

# 🔄 Request Flow

User → Internet → Web Server  
↓ Reverse Proxy  
App Server  
↓  
DB Server  

---

# 🚀 High Availability

- Web Servers in **Auto Scaling Group**
- Reverse Proxy → internal routing
- Elastic IP on App Server  

---

## 📷 Auto Scaling

<img width="1919" height="858" alt="autoscaling" src="https://github.com/user-attachments/assets/ced15d3c-4c4b-4832-a94e-e206575590b4" />


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

## 📷 Application UI

<img width="1919" height="868" alt="Application" src="https://github.com/user-attachments/assets/14f23a80-f825-43d3-9b07-4700a978cb02" />


---

# ⚙️ Deployment Steps

---

## 🟢 Web Server Setup

```bash
sudo apt update
sudo apt install nginx -y
sudo nano /var/www/html/index.html
```

### Reverse Proxy

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

## 🟡 App Server Setup

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

```ini
bind-address = 0.0.0.0
```

```bash
sudo systemctl restart mariadb
sudo systemctl restart nginx
sudo systemctl restart php8.3-fpm
```

---

## 🔵 Database Server Setup

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
CREATE USER 'appuser'@'10.0.2.%' IDENTIFIED BY 'Pass@123';
GRANT ALL PRIVILEGES ON expense_app.* TO 'appuser'@'10.0.2.%';

FLUSH PRIVILEGES;
```
Enable App connections:

```bash
sudo nano /etc/mysql/mariadb.conf.d/50-server.cnf
```

```ini
bind-address = 0.0.0.0
```

```bash
sudo systemctl restart mariadb
```

---

# 🧪 API Test

```bash
curl -X POST -H "Content-Type: application/json" \
-d '{"title":"Tea","amount":10}' \
"http://APP_ELASTIC_IP/api.php?action=add"
```

---

# 🔐 Security Groups Configuration

## 🟢 Web Server Security Group (web-sg)

| Type | Port | Source | Purpose |
|---|---|---|---|
| SSH | 22 | My IP | Admin access |
| HTTP | 80 | 0.0.0.0/0 | Public website access |

---

## 🟡 App Server Security Group (app-sg)

| Type | Port | Source | Purpose |
|---|---|---|---|
| SSH | 22 | web-sg | SSH via Web Server |
| HTTP | 80 | web-sg | Reverse proxy traffic |
| MySQL/Aurora | 3306 | db-sg | Database connection |

---

## 🔵 Database Server Security Group (db-sg)

| Type | Port | Source | Purpose |
|---|---|---|---|
| SSH | 22 | app-sg | Admin via App Server |
| MySQL/Aurora | 3306 | app-sg | DB access only from App Tier |

---

## 📷 Security Groups

<img width="1024" height="1536" alt="AWS Security Group Inbound Rules Overview" src="https://github.com/user-attachments/assets/27c67e73-51eb-4486-8e1b-0be18ad15499" />


---

# 🌐 Live Application

http://YOUR_WEB_SERVER_PUBLIC_IP

---

# 📌 Key Learning Outcomes

- AWS VPC & Subnet Architecture  
- Bastion Host (SSH Jump Server)  
- Reverse Proxy (Nginx)  
- Auto Scaling Deployment  
- Private Networking Security  

---

# 👩‍💻 Author

**Pratiksha Lavand**

🔗 LinkedIn:  https://www.linkedin.com/in/pratiksha-lavand/
🔗 GitHub: https://github.com/pratikshalavand98/

---

⭐ Give this repo a star if you like it!
