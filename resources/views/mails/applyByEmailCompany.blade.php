@component('mail::message')
Dear {{ $this->user->name }}
Thank you for applying for the position of {{ $this->job->title }} at {{ $this->company->name }}. We have received your application and it is currently under review.
If your qualifications align with our requirements, we will be in touch regarding the next steps. Thank you for considering {{ $this->company->name }} as your potential employer.
Thank you for choosing {{ config('app.name') }} 

@endcomponent
