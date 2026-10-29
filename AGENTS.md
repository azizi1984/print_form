# Workspace Rules for AI Agents

All guidelines defined in `agent.md` apply to this project.

## Pre-approved & Auto-Allowed Operations (อนุญาตให้ทำงานได้ทันทีโดยไม่ต้องขออนุญาต)

The AI agent is explicitly authorized to run the following diagnostic and status check operations without requesting user confirmation or approval:

1. **Check PHP version** (e.g., `php -v`, `docker compose exec app php -v`)
2. **Check Docker version** (e.g., `docker --version`, `docker compose version`)
3. **Check Git status** (e.g., `git status`)
4. **Check diff of docker-compose.yml** (e.g., `git diff docker-compose.yml`)

## Git Workflow Rules (ข้อกำหนดการทำงานกับ Git)
- **Automatic Commit on Push:** เมื่อผู้ใช้มีคำสั่ง `git push` ให้ดำเนินการทำ `git commit` ให้ด้วยเสมอ โดยต้องแยก commit ออกจากกันตาม function หรือ module อย่างชัดเจน ก่อนที่จะรันคำสั่ง `git push`
- **Commit Separation:** ห้ามรวมการแก้ไขหลาย function/feature ที่ไม่เกี่ยวข้องกันไว้ใน commit เดียว ให้ทำการ `git add` และ `git commit` แยกทีละส่วนตามหน้าที่การทำงาน (function/module)
- **No Unsolicited Push:** ห้ามสั่ง `git push` หรือ `git commit` เองโดยพลการหากผู้ใช้ไม่ได้สั่งหรือร้องขอ

## General Development Rules
- **Language for Documentation:** Plans, walkthroughs, and explanations must be in Thai (ภาษาไทย).
- **Layout Reuse:** Use `layouts.app` and `layouts.topbar` for all blade views.
- **Global UI Notification & Confirmation Standard:** ห้ามใช้ Browser Native `alert()` และ `confirm()` ใน JavaScript หรือ Blade เด็ดขาด ให้ใช้ `window.showToast()`, `window.showConfirmModal()`, หรือ `window.showAlertModal()` ที่รวมไว้ใน `layouts.app` เสมอ เพื่อรักษามาตรฐาน UI/UX เดียวกันทั้งระบบ
- **Master Data Caching Rule (Business Logic):** ข้อมูล Master หรือ Metadata ที่อ่านซ้ำบ่อยและไม่ค่อยเปลี่ยนแปลง (เช่น `sysfield_ex`) ต้องกำหนดระยะเวลา Cache 24 ชั่วโมง ผ่าน Eloquent Model Helper พร้อมกลไก Invalidate อัตโนมัติเมื่อมีการเปลี่ยนแปลง
- **Central Template Priority Rule (Business Logic):** หน้า Index ของเทมเพลตทุกประเภท (Header, Detail, Footer, Profile) ต้องจัดเรียงข้อมูลที่สร้างจากส่วนกลาง (`profile_id = 'ZZ00'`) แสดงเป็น Record แรกเสมอ พร้อม Badge "ส่วนกลาง" และตั้งค่า `order: []` ใน DataTable
- **Modular Blade Design:** หลีกเลี่ยง Monolithic Blade Files ขนาดใหญ่ในหน้าที่ซับซ้อน ให้แยก UI และ Logic ย่อยออกเป็น Blade Partials ในโฟลเดอร์ `partials/` เสมอ
