<?php

namespace App\Http\Resources\Counseling;

use Illuminate\Http\Resources\Json\JsonResource;

class CounselingBookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $session  = $this->counselingSession;
        $counselor = $session ? $session->counselor : null;

        return [
            'id'           => $this->id,
            'booking_date' => $this->booking_date ? $this->booking_date->toDateString() : null,
            'start_time'   => $this->start_time,
            'end_time'     => $this->end_time,
            'status'       => $this->status,
            'notes'        => $this->notes,

            // Session info
            'session' => $session ? [
                'id'          => $session->id,
                'title'       => $session->title,
                'description' => $session->description,
                'fee'         => (float) $session->fee,
                // Zoom info — relevant once booking is confirmed
                'zoom_link'       => $session->zoom_link,
                'zoom_meeting_id' => $session->zoom_meeting_id,
                'zoom_passcode'   => $session->zoom_passcode,
            ] : null,

            // Counselor info
            'counselor' => $counselor ? [
                'id'             => $counselor->id,
                'name'           => $counselor->user ? $counselor->user->name : null,
                'specialization' => $counselor->specialization,
                'photo_url'      => $counselor->image_url,
            ] : null,

            // Review (if already submitted)
            'review' => $this->whenLoaded('review', function () {
                return $this->review ? [
                    'id'      => $this->review->id,
                    'rating'  => $this->review->rating,
                    'comment' => $this->review->comment,
                ] : null;
            }),

            'created_at' => $this->created_at ? $this->created_at->toDateTimeString() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toDateTimeString() : null,
        ];
    }
}
