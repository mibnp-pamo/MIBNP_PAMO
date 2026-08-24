NEW VISITATION REQUEST

Name: {{ $details['name'] }}
Email: {{ $details['email'] }}
Phone: {{ $details['phone'] ?: 'Not provided' }}
Organization: {{ $details['organization'] ?: 'Not provided' }}
Activity: {{ $details['activity'] }}
Intended date: {{ $details['intended_date'] }}
Number of visitors: {{ $details['visitor_count'] }}
Document type: {{ $details['document_type'] ?: 'No document attached' }}

Additional information:
{{ $details['message'] ?: 'No additional information provided.' }}

Reply directly to this message to contact {{ $details['name'] }}.
