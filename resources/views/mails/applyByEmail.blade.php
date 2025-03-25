@component('mail::message')
<h4>Dear {{ $userName }}</h4>
<p>Thank you for applying for the position of {{ $jobTitle }} at {{ $companyName }}. We have received your application and it is currently under review.</p>
<p>If your qualifications align with our requirements, we will be in touch regarding the next steps. Thank you for considering {{ $companyName }} as your potential employer.</p>
<p>Thank you for choosing {{ config('app.name') }} </p>

@endcomponent
