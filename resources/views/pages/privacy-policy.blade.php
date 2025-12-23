<x-guest-layout>
    <div class="min-h-screen w-full flex flex-col justify-start items-center py-[5rem] px-4 lg:px-0">
        <article class="max-w-[800px] w-full prose prose-lg dark:prose-invert">
            <h1 class="text-3xl font-bold text-text mb-8">Privacy Policy</h1>

            <p class="text-sm text-muted mb-8">
                Last updated: {{ now()->format('F d, Y') }}
            </p>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-text mb-4">1. Introduction</h2>
                <p class="text-text mb-4">
                    Welcome to {{ config('app.name') }}. I respect your privacy and am committed to protecting your
                    personal data.
                    This privacy policy explains how I collect, use, and safeguard your information when you visit my
                    website.
                </p>
                <p class="text-text">
                    This website is operated by Luca Macedone, based in Italy. As such, this policy complies with the
                    General Data Protection Regulation (GDPR) and Italian data protection laws.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-text mb-4">2. Data Controller</h2>
                <p class="text-text">
                    The data controller responsible for your personal data is:<br>
                    <strong>Luca Macedone</strong><br>
                    Email: <a href="mailto:{{ config('contact.admin_email') }}"
                        class="text-accent hover:underline">{{ config('contact.admin_email') }}</a>
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-text mb-4">3. Information I Collect</h2>

                <h3 class="text-lg font-medium text-text mb-2">3.1 Contact Form Data</h3>
                <p class="text-text mb-4">
                    When you submit the contact form, I collect:
                </p>
                <ul class="list-disc list-inside text-text mb-4 space-y-1">
                    <li>Your name</li>
                    <li>Your email address</li>
                    <li>Subject of your message</li>
                    <li>Message content</li>
                    <li>IP address (for security and spam prevention)</li>
                    <li>User agent (browser information)</li>
                </ul>

                <h3 class="text-lg font-medium text-text mb-2">3.2 Technical Data</h3>
                <p class="text-text">
                    When you browse the website, the server may automatically collect technical information such as
                    your IP address, browser type, and pages visited. This data is used solely for security purposes
                    and website functionality.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-text mb-4">4. How I Use Your Data</h2>
                <p class="text-text mb-4">I use the collected data for the following purposes:</p>
                <ul class="list-disc list-inside text-text space-y-1">
                    <li>To respond to your inquiries and messages</li>
                    <li>To send you an automatic confirmation email after you submit the contact form</li>
                    <li>To prevent spam and abuse of the contact form</li>
                    <li>To maintain the security and functionality of the website</li>
                    <li>To comply with legal obligations</li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-text mb-4">5. Legal Basis for Processing</h2>
                <p class="text-text mb-4">Under GDPR, I process your personal data based on:</p>
                <ul class="list-disc list-inside text-text space-y-1">
                    <li><strong>Consent</strong>: When you voluntarily submit the contact form</li>
                    <li><strong>Legitimate interest</strong>: For security purposes and spam prevention</li>
                    <li><strong>Legal obligation</strong>: When required by law</li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-text mb-4">6. Data Retention</h2>
                <p class="text-text">
                    Contact form submissions are retained for a maximum of
                    <strong>{{ config('contact.delete_after_days', 365) }} days</strong>,
                    after which they are automatically deleted. Read messages may be archived after
                    {{ config('contact.archive_read_after_days', 30) }} days.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-text mb-4">7. Data Sharing</h2>
                <p class="text-text">
                    I do not sell, trade, or share your personal data with third parties, except:
                </p>
                <ul class="list-disc list-inside text-text space-y-1">
                    <li>When required by law or legal process</li>
                    <li>To hosting service providers who help operate this website (under strict confidentiality
                        agreements)</li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-text mb-4">8. Your Rights (GDPR)</h2>
                <p class="text-text mb-4">Under GDPR, you have the following rights:</p>
                <ul class="list-disc list-inside text-text space-y-1">
                    <li><strong>Right of access</strong>: Request a copy of your personal data</li>
                    <li><strong>Right to rectification</strong>: Request correction of inaccurate data</li>
                    <li><strong>Right to erasure</strong>: Request deletion of your data ("right to be forgotten")</li>
                    <li><strong>Right to restrict processing</strong>: Request limitation of data processing</li>
                    <li><strong>Right to data portability</strong>: Request your data in a structured format</li>
                    <li><strong>Right to object</strong>: Object to processing based on legitimate interest</li>
                    <li><strong>Right to withdraw consent</strong>: Withdraw consent at any time</li>
                </ul>
                <p class="text-text mt-4">
                    To exercise any of these rights, please contact me at
                    <a href="mailto:{{ config('contact.admin_email') }}"
                        class="text-accent hover:underline">{{ config('contact.admin_email') }}</a>.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-text mb-4">9. Cookies</h2>
                <p class="text-text">
                    This website uses only essential technical cookies necessary for the proper functioning of the site
                    (such as session cookies). These cookies do not collect personal information for marketing purposes
                    and are exempt from consent requirements under GDPR.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-text mb-4">10. Security</h2>
                <p class="text-text">
                    I implement appropriate technical and organizational measures to protect your personal data against
                    unauthorized access, alteration, disclosure, or destruction. These measures include encryption,
                    secure servers, and access controls.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-text mb-4">11. Changes to This Policy</h2>
                <p class="text-text">
                    I may update this privacy policy from time to time. Any changes will be posted on this page with
                    an updated revision date. I encourage you to review this policy periodically.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-text mb-4">12. Contact</h2>
                <p class="text-text">
                    If you have any questions about this privacy policy or wish to exercise your rights,
                    please contact me at:<br><br>
                    <strong>Email:</strong> <a href="mailto:{{ config('contact.admin_email') }}"
                        class="text-accent hover:underline">{{ config('contact.admin_email') }}</a>
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-text mb-4">13. Supervisory Authority</h2>
                <p class="text-text">
                    If you believe your data protection rights have been violated, you have the right to lodge a
                    complaint
                    with the Italian Data Protection Authority (Garante per la protezione dei dati personali):<br><br>
                    <strong>Website:</strong> <a href="https://www.garanteprivacy.it" target="_blank"
                        rel="noopener noreferrer" class="text-accent hover:underline">www.garanteprivacy.it</a>
                </p>
            </section>
        </article>
    </div>
</x-guest-layout>
