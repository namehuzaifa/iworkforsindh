<?php

namespace App\Http\Resources\Counseling;

use Illuminate\Http\Resources\Json\JsonResource;

class CounselingSessionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $counselor = $this->counselor;
        $counselorUser = $counselor ? $counselor->user : null;

        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'fee'         => (float) $this->fee,
            'is_active'   => $this->is_active,

            // Zoom info (only for booked candidates — ya hamesha bhej do, app side decide kare)
            'zoom_link'       => $this->zoom_link,
            'zoom_meeting_id' => $this->zoom_meeting_id,
            'zoom_passcode'   => $this->zoom_passcode,

            // Category
            'category' => $this->whenLoaded('counselingCategory', function () {
                return [
                    'id'   => $this->counselingCategory->id,
                    'name' => $this->counselingCategory->name,
                    'slug' => $this->counselingCategory->slug,
                ];
            }),

            // Counselor info
            'counselor' => $counselor ? [
                'id'               => $counselor->id,
                'name'             => $counselorUser ? $counselorUser->name : null,
                'specialization'   => $counselor->specialization,
                'bio'              => $counselor->bio,
                'experience_years' => $counselor->experience_years,
                'photo_url'        => $counselor->image_url,
            ] : null,

            // Available days/schedules
            'schedules' => $this->whenLoaded('schedules', function () {
                return $this->schedules->map(function ($schedule) {
                    return [
                        'day_of_week' => $schedule->day_of_week,
                        'day_name'    => $schedule->day_name,
                        'start_time'  => $schedule->start_time,
                        'end_time'    => $schedule->end_time,
                    ];
                });
            }),

            // Available day numbers array (for calendar highlighting)
            'available_days' => $this->whenLoaded('schedules', function () {
                return $this->schedules->pluck('day_of_week')->values();
            }),

            // Ratings summary
            'reviews_count'  => $this->whenLoaded('reviews', function () {
                return $this->reviews->count();
            }),
            'average_rating' => $this->whenLoaded('reviews', function () {
                return $this->reviews->count()
                    ? round($this->reviews->avg('rating'), 1)
                    : null;
            }),

            'bookings_count' => $this->when(isset($this->bookings_count), $this->bookings_count),

            'created_at' => $this->created_at ? $this->created_at->toDateTimeString() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toDateTimeString() : null,
        ];
    }
}
