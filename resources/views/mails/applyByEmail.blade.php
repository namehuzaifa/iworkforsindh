@component('mail::message')
Dear {{ $user->name }}
Thank you for applying for the position of {{ $job->title }} at {{ $company->name }}. We have received your application and it is currently under review.
If your qualifications align with our requirements, we will be in touch regarding the next steps. Thank you for considering {{ $company->name }} as your potential employer.
Thank you for choosing {{ config('app.name') }} 

@endcomponent
