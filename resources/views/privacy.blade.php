@extends('layouts.site')

@section('content')
    <main>
        <section class="section">
            <div class="container privacy-layout">
                <article class="text-panel">
                    <p class="section-tag">Website privacy</p>
                    <h1>Privacy notice</h1>
                    <p>
                        This website is operated for the Protected Area Management Office of Mounts Iglit-Baco
                        Natural Park. This notice explains how information submitted through the website is handled.
                    </p>

                    <h2>Information collected</h2>
                    <p>
                        A visitation request may include your name, email address, phone number, organization,
                        planned activity and date, visitor count, message, and an optional supporting PDF.
                        The website also receives ordinary technical information such as an IP address, browser
                        details, request time, and requested page in its operational logs.
                    </p>

                    <h2>Why the information is used</h2>
                    <p>
                        Submitted information is used to review, coordinate, and respond to requests to visit or
                        conduct activities in the protected area. Technical logs are used to keep the website secure,
                        diagnose faults, and prevent abuse.
                    </p>

                    <h2>How requests are handled</h2>
                    <p>
                        The application sends completed requests to the PAMO office by email. Uploads are processed
                        temporarily for delivery and are not published on the website. Authorized personnel and the
                        service providers needed to operate the website and email service may process the information.
                    </p>

                    <h2>Retention and your choices</h2>
                    <p>
                        The PAMO office retains requests and operational records only as required for visitor
                        coordination, security, and applicable government records-management obligations. You may
                        ask about your information, request a correction, or withdraw a request by contacting
                        <a href="mailto:r4b.mibnp@denr.gov.ph">r4b.mibnp@denr.gov.ph</a>.
                    </p>

                    <h2>Third-party map and font services</h2>
                    <p>
                        Some pages load OpenStreetMap tiles and Google-hosted fonts. When those resources load,
                        the provider receives the technical information required to deliver them, including your
                        IP address. External websites linked from this site apply their own privacy practices.
                    </p>

                    <p>
                        This notice should be reviewed whenever the form, hosting provider, email provider, or
                        records-retention procedure changes.
                    </p>
                </article>

                <aside class="privacy-sidebar site-side-panel" aria-labelledby="privacy-sidebar-title">
                    <p class="section-tag">At a glance</p>
                    <h2 id="privacy-sidebar-title">Your visitation details stay with the office</h2>
                    <ul>
                        <li>Requests are sent to PAMO for coordination and response.</li>
                        <li>Supporting PDFs are processed for delivery and are not published.</li>
                        <li>You can ask to correct or withdraw a request at any time.</li>
                    </ul>
                    <a class="button button-secondary button-full" href="mailto:r4b.mibnp@denr.gov.ph">
                        Contact the office
                    </a>
                </aside>
            </div>
        </section>
    </main>
@endsection
