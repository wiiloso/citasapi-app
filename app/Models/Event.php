<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Event",
 *     type="object",
 *     title="Event",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Event Title"),
 *     @OA\Property(property="start", type="string", format="date-time", example="2023-01-01T10:00:00Z"),
 *     @OA\Property(property="end", type="string", format="date-time", example="2023-01-01T12:00:00Z"),
 *     @OA\Property(property="color", type="string", example="#ff0000"),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="doctor_id", type="integer", example=1),
 *     @OA\Property(property="consultorio_id", type="integer", example=1)
 * )
 */

class Event extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable =
    [
        'id',
        'title',
        'start',
        'end',
        'color',
        'user_id',
        'doctor_id',
        'consultorio_id'
    ];
}

// -- id, title, start, end, color, user_id, doctor_id, consultorio_id, created_at, updated_at