@component('mail::message')

<h4>Dear Employer's,</h4>

<p>We are pleased to inform you that {{ $userName }} has applied for the {{ $jobTitle }} position at your esteemed organization through the I Work for Sindh platform.</p>
<p>Below are the candidate's details for your consideration</p>

<ul>
    <li>Name: {{ $userName }}</li>
    <li>Email: {{ $userEmail }}</li>
    <li>Contact Number: {{ $userPhone }}</li>
</ul>

<x-mail::button :url="$userCVULR">
    {{ $userName }} resume
</x-mail::button>

<p>Attached to this email, you will find the candidate’s complete CV and relevant details. We encourage you to review the application and consider them for the position.</p>

<p>For any further queries, please feel free to reach out.</p>

<p>Best regards</p>
<h4>I Work for Sindh Team</h4>

@endcomponent
