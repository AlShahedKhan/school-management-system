<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolAdmitCardSetting extends Model
{
    public const DEFAULT_EN = [
        'Candidates must arrive at the examination hall at least 15 minutes before the examination begins.',
        'Candidates must bring this admit card and may not carry unauthorized materials into the examination hall.',
        'Candidates must follow the instructions of the invigilator throughout the examination.',
    ];

    public const DEFAULT_BN = [
        'পরীক্ষা শুরুর কমপক্ষে ১৫ মিনিট আগে পরীক্ষার হলে উপস্থিত হতে হবে।',
        'প্রবেশপত্র সঙ্গে আনতে হবে এবং পরীক্ষার হলে কোনো অননুমোদিত সামগ্রী বহন করা যাবে না।',
        'পরীক্ষা চলাকালীন পরিদর্শকের নির্দেশনা মেনে চলতে হবে।',
    ];

    protected $fillable = ['school_id', 'instructions_en', 'instructions_bn'];

    protected function casts(): array
    {
        return [
            'instructions_en' => 'array',
            'instructions_bn' => 'array',
        ];
    }

    public static function forSchool(int $schoolId): self
    {
        return static::firstOrCreate(
            ['school_id' => $schoolId],
            ['instructions_en' => self::DEFAULT_EN, 'instructions_bn' => self::DEFAULT_BN]
        );
    }
}
