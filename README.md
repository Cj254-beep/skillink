# SkillLink (prototype)

Student internship and job matching platform. BCS3212 Agile Software Development.

## Features in this build (Sprint 1 and 2)
- Register and log in as a student or employer
- Student profile: course, year, skills, CV upload
- Employer posts an opening with required skills and a closing date
- Student sees openings ranked by how many required skills they have
- Student applies and tracks the status of each application

## Team
- James Mwanzia (BSCCS/2024/74071)
- Munga Margret (BSCCS/2024/50177)
- Hezron Karithi (BSCCS/2024/73578)
- Rooney Samuel (BSCCS/2024/60056)
- Brian Kiptui (BSCCS/2024/53968)

## Run it locally (XAMPP)
1. Install XAMPP and start **Apache** and **MySQL**.
2. Copy this folder to `C:\xampp\htdocs\skilllink`.
3. Open http://localhost/phpmyadmin, go to **Import**, choose `schema.sql`, and run it.
4. Open http://localhost/skilllink/
5. If your MySQL has a password, edit the database line in `includes/config.php`.

## Try it
1. Create an employer account and post an opening with skills like `php, mysql, html`.
2. Log out, create a student account, and fill in your profile with at least five skills.
3. Open **Openings**: the opening you posted shows how many skills match. Click **Apply**.
4. Open **My applications** to see the status.

## Not built yet (backlog)
Employer review of applicants, search and filter, notifications, coordinator dashboard, admin moderation.
