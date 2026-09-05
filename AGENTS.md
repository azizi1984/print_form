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
