<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Attendance Statuses
    |--------------------------------------------------------------------------
    |
    | The following statuses are used throughout the attendance system.
    | You may modify these as needed for your application.
    |
    */

    'absensi_statuses' => [
        'Hadir' => 'Hadir',
        'Telat' => 'Telat',
        'Sakit' => 'Sakit',
        'Izin' => 'Izin',
        'Alpa' => 'Alpa',
    ],

    /*
    |--------------------------------------------------------------------------
    | Present Statuses
    |--------------------------------------------------------------------------
    |
    | These statuses are considered as "present" for reporting purposes.
    |
    */

    'absensi_present_statuses' => [
        'Hadir',
    ],

    /*
    |--------------------------------------------------------------------------
    | Excused Statuses
    |--------------------------------------------------------------------------
    |
    | These statuses are considered as "excused" absences.
    |
    */

    'absensi_excused_statuses' => [
        'Sakit',
        'Izin',
    ],

    /*
    |--------------------------------------------------------------------------
    | Absent Status
    |--------------------------------------------------------------------------
    |
    | This status is considered as "absent" (unexcused).
    |
    */

    'absensi_absent_status' => 'Alpa',

    /*
    |--------------------------------------------------------------------------
    | Correction Statuses
    |--------------------------------------------------------------------------
    */

    'correction_statuses' => [
        'hadir' => 'Hadir',
        'sakit_izin' => 'Sakit/Izin',
        'alpa' => 'Alpa',
    ],

    /*
    |--------------------------------------------------------------------------
    | Correction Approval Statuses
    |--------------------------------------------------------------------------
    */

    'correction_approval_statuses' => [
        'pending' => 'Pending',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak',
    ],

    /*
    |--------------------------------------------------------------------------
    | Correction to Attendance Mapping
    |--------------------------------------------------------------------------
    |
    | Maps correction statuses to actual attendance statuses when approved.
    |
    */

    'correction_to_absensi' => [
        'hadir' => 'Hadir',
        'sakit_izin' => 'Izin',
        'alpa' => 'Alpa',
    ],

];