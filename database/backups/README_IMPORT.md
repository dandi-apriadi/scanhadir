# Database Backup Import Guide

Backup files in this folder are generated from the local `scanhadir` MySQL database.

## Import on New Device (Laragon)

1. Ensure MySQL is running.
2. Create the target database (if not yet created):

```powershell
C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS scanhadir CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

3. Import the backup file:

```powershell
C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe -u root scanhadir < database/backups/scanhadir_YYYYMMDD_HHMMSS.sql
```

4. Verify tables:

```powershell
C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe -u root -e "USE scanhadir; SHOW TABLES;"
```

## Notes

- Replace `scanhadir_YYYYMMDD_HHMMSS.sql` with the backup filename you want to import.
- If your MySQL user has a password, add `-p` and enter the password when prompted.
