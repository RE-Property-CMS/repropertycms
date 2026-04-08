@extends('layouts.default')

@section('content')
<div class="w-100 h-100" style='background-image: url("{{ asset('images/auth-background.jpg') }}"); padding:100px 0px;background-size:cover;'>
        <div class="container py-1" >
                <div class="card shadow-lg p-4 rounded-lg agentsign" style="background-color:#39393c; color: white; font-family: 'Tenor Sans;">
                        <h1 class="text-center mb-4">Terms and Conditions</h1>
                        <p>These terms and conditions (“Agreement”) set forth the general terms and conditions of your use of the <strong>{{ config('app.name') }}</strong> website (“Website”), <strong>https://www.{{ config('app.name') }}</strong> mobile application (“Mobile Application”) and any of their related products and services (collectively, “Services”). This Agreement is legally binding between you (“User”, “you” or “your”) and {{ config('app.name') }} (“{{ config('app.name') }}”, “we”, “us” or “our”). If you are entering into this Agreement on behalf of a business or other legal entity, you represent that you have the authority to bind such entity to this Agreement, in which case the terms “User”, “you” or “your” shall refer to such entity. If you do not have such authority, or if you do not agree with the terms of this Agreement, you must not accept this Agreement and may not access and use the Services. By accessing and using the Services, you acknowledge that you have read, understood, and agree to be bound by the terms of this Agreement. You acknowledge that this Agreement is a contract between you and {{ config('app.name') }}, even though it is electronic and is not physically signed by you, and it governs your use of the Services.</p>

                        <h4>Free until you publish your website.</h4>
                        <p>At {{ config('app.name') }}, you register, sign in and start to build your property website for free, no credit card required.  When you publish your website which allows you to share to the public, you will select your monthly subscription.</p>

                        <h4>{{ config('app.name') }} Monthly Subscriptions</h4><br>
                        <h4>Payment Terms for Publishing your Property Website</h4>
                        <p>Upon publishing your property website, your subscription will become active and your credit card will be charged for the first month (billing period). Your credit card will be charged on the same day each month that you originally signed up on or the nearest business day.</p>

                        <h4>Subscription Plan Changes - Upgrade/Downgrade your Plan</h4>
                        <ul>
                                <li>1 Property - <strong>$29.00</strong> per month</li>
                                <li>5 Properties - <strong>$53.00</strong> per month</li>
                                <li>30 Properties - <strong>$130.00</strong> per month</li>
                        </ul>

                        <p>If you make changes to your subscription plan (upgrade or downgrade) midway through your billing cycle nothing is billed at that time. Then on your next month's billing day the plan changes you made are prorated based on the number of days that remained in the prior billing period.</p>
                        <p>If you downgraded your plan then a credit is calculated that reduces your next bill, or if you upgrade your plan then our system collects the prorated difference based on the numbers of days you were on the higher plan in the last billing cycle and it also bills for the upcoming months charges. Your invoice will include extra line item(s) for these prorations.
                        <br>
                            You can then delete the property websites you no longer require and publish the ones you require.
                        </p>
                        <p>You must be at least 18 years old to use our Services. You are responsible for maintaining the security of your account. {{ config('app.name') }} is not liable for any unauthorized use of your account.</p>

                        <h4>Cancelling your Subscription</h4>
                        <p>If you choose to cancel a subscription we do NOT issue refunds for days remaining in a billing cycle.</p>

                        <h4>Accounts and membership</h4>
                        <p>You must be at least 18 years of age to use the Services. By using the Services and by agreeing to this Agreement you warrant and represent that you are at least 18 years of age. If you create an account on the Services, you are responsible for maintaining the security of your account and you are fully responsible for all activities that occur under the account and any other actions taken in connection with it. We may monitor and review new accounts before you may sign in and start using the Services. Providing false contact information of any kind may result in the termination of your account. You must immediately notify us of any unauthorized uses of your account or any other breaches of security. We will not be liable for any acts or omissions by you, including any damages of any kind incurred as a result of such acts or omissions. We may suspend, disable, or delete your account (or any part thereof) if we determine that you have violated any provision of this Agreement or that your conduct or content would tend to damage our reputation and goodwill. If we delete your account for the foregoing reasons, you may not re-register for our Services. We may block your email address and Internet protocol address to prevent further registration.</p>

                        <h4>Delete a property website and your subscription plan</h4>
                        <p>All trademarks, content, and intellectual property on the Services are owned by {{ config('app.name') }} or its licensors. Unauthorized use is strictly prohibited.</p>

                        <h4>9. Changes to Terms</h4>
                        <p>Please note: If you delete website(s) it does NOT automatically downgrade your subscription plan.</p>
                        <p>If you don't intend to add new websites to replace any that you deleted then you should go to the Subscription Portal button.  My Subscription Plan screen and downgrade to a less expensive plan if eligible based on your new usage.</p>
                        <p>As long as your subscription is active, your property subscription count stays open month to month.</p>

                        <ul>
                                <li>1 Property - <strong>$29.00</strong> per month</li>
                                <li>5 Properties - <strong>$53.00</strong> per month</li>
                                <li>30 Properties - <strong>$130.00</strong> per month</li>
                        </ul>

                        <h4>Delete the property website</h4>
                        <p>This will permanently delete the property website along with all associated text data and photos.</p>

                        <h4>User content</h4>
                        <p>We do not own any data, information or material (collectively, “Content”) that you submit on the Services in the course of using the Service. You shall have sole responsibility for the accuracy, quality, integrity, legality, reliability, appropriateness, and intellectual property ownership or right to use of all submitted Content. We may, but have no obligation to, monitor and review the Content on the Services submitted or created using our Services by you. You grant us permission to access, copy, distribute, store, transmit, reformat, display and perform the Content of your user account solely as required for the purpose of providing the Services to you. Without limiting any of those representations or warranties, we have the right, though not the obligation, to, in our own sole discretion, refuse or remove any Content that, in our reasonable opinion, violates any of our policies or is in any way harmful or objectionable. You also grant us the license to use, reproduce, adapt, modify, publish or distribute the Content created by you or stored in your user account for commercial, marketing or any similar purpose.</p>

                        <h4>Billing and payments</h4>
                        <p>You shall pay all fees or charges to your account in accordance with the fees, charges, and billing terms in effect at the time a fee or charge is due and payable. Auto-renewal is enabled for the Services you have subscribed for, your payment information will be securely saved and you will be charged automatically in accordance with the term you selected. If, in our judgment, your purchase constitutes a high-risk transaction, we will require you to provide us with a copy of your valid government-issued photo identification, and possibly a copy of a recent bank statement for the credit or debit card used for the purchase. We reserve the right to change products and product pricing at any time. We also reserve the right to refuse any order you place with us. We may, in our sole discretion, limit or cancel quantities purchased per person, per household or per order. These restrictions may include orders placed by or under the same customer account, the same credit card, and/or orders that use the same billing and/or shipping address. In the event that we make a change to or cancel an order, we may attempt to notify you by contacting the email and/or billing address/phone number provided at the time the order was made.</p>

                        <h4>Accuracy of information</h4>
                        <p>Occasionally there may be information on the Services that contains typographical errors, inaccuracies or omissions that may relate to product descriptions, pricing, availability, promotions and offers. We reserve the right to correct any errors, inaccuracies or omissions, and to change or update information or cancel orders if any information on the Services or Services is inaccurate at any time without prior notice (including after you have submitted your order). We undertake no obligation to update, amend or clarify information on the Services including, without limitation, pricing information, except as required by law. No specified update or refresh date applied on the Services should be taken to indicate that all information on the Services or Services has been modified or updated.</p>

                        <h4>Backups</h4>
                        <p>We perform regular backups of the Website and its Content and will do our best to ensure completeness and accuracy of these backups. In the event of the hardware failure or data loss we will restore backups automatically to minimize the impact and downtime.</p>

                        <h4>Links to other resources</h4>
                        <p>Although the Services may link to other resources (such as websites, mobile applications, etc.), we are not, directly or indirectly, implying any approval, association, sponsorship, endorsement, or affiliation with any linked resource, unless specifically stated herein. We are not responsible for examining or evaluating, and we do not warrant the offerings of, any businesses or individuals or the content of their resources. We do not assume any responsibility or liability for the actions, products, services, and content of any other third parties. You should carefully review the legal statements and other conditions of use of any resource which you access through a link on the Services. Your linking to any other off-site resources is at your own risk.</p>

                        <h4>Prohibited uses</h4>
                        <p>In addition to other terms as set forth in the Agreement, you are prohibited from using the Services or Content: (a) for any unlawful purpose; (b) to solicit others to perform or participate in any unlawful acts; (c) to violate any international, federal, provincial or state regulations, rules, laws, or local ordinances; (d) to infringe upon or violate our intellectual property rights or the intellectual property rights of others; (e) to harass, abuse, insult, harm, defame, slander, disparage, intimidate, or discriminate based on gender, sexual orientation, religion, ethnicity, race, age, national origin, or disability; (f) to submit false or misleading information; (g) to upload or transmit viruses or any other type of malicious code that will or may be used in any way that will affect the functionality or operation of the Services, third party products and services, or the Internet; (h) to spam, phish, pharm, pretext, spider, crawl, or scrape; (i) for any obscene or immoral purpose; or (j) to interfere with or circumvent the security features of the Services, third party products and services, or the Internet. We reserve the right to terminate your use of the Services for violating any of the prohibited uses.</p>

                        <h4>Intellectual property rights</h4>
                        <p>“Intellectual Property Rights” means all present and future rights conferred by statute, common law or equity in or in relation to any copyright and related rights, trademarks, designs, patents, inventions, goodwill and the right to sue for passing off, rights to inventions, rights to use, and all other intellectual property rights, in each case whether registered or unregistered and including all applications and rights to apply for and be granted, rights to claim priority from, such rights and all similar or equivalent rights or forms of protection and any other results of intellectual activity which subsist or will subsist now or in the future in any part of the world. This Agreement does not transfer to you any intellectual property owned by {{ config('app.name') }} or third parties, and all rights, titles, and interests in and to such property will remain (as between the parties) solely with {{ config('app.name') }}. All trademarks, service marks, graphics and logos used in connection with the Services, are trademarks or registered trademarks of {{ config('app.name') }} or its licensors. Other trademarks, service marks, graphics and logos used in connection with the Services may be the trademarks of other third parties. Your use of the Services grants you no right or license to reproduce or otherwise use any of {{ config('app.name') }} or third party trademarks.</p>

                        <h4>Limitation of liability</h4>
                        <p>To the fullest extent permitted by applicable law, in no event will {{ config('app.name') }}, its affiliates, directors, officers, employees, agents, suppliers or licensors be liable to any person for any indirect, incidental, special, punitive, cover or consequential damages (including, without limitation, damages for lost profits, revenue, sales, goodwill, use of content, impact on business, business interruption, loss of anticipated savings, loss of business opportunity) however caused, under any theory of liability, including, without limitation, contract, tort, warranty, breach of statutory duty, negligence or otherwise, even if the liable party has been advised as to the possibility of such damages or could have foreseen such damages. To the maximum extent permitted by applicable law, the aggregate liability of {{ config('app.name') }} and its affiliates, officers, employees, agents, suppliers and licensors relating to the services will be limited to an amount no greater than one dollar or any amounts actually paid in cash by you to {{ config('app.name') }} for the prior one month period prior to the first event or occurrence giving rise to such liability. The limitations and exclusions also apply if this remedy does not fully compensate you for any losses or fails of its essential purpose.</p>

                        <h4>Indemnification</h4>
                        <p>You agree to indemnify and hold {{ config('app.name') }} and its affiliates, directors, officers, employees, agents, suppliers and licensors harmless from and against any liabilities, losses, damages or costs, including reasonable attorneys’ fees, incurred in connection with or arising from any third party allegations, claims, actions, disputes, or demands asserted against any of them as a result of or relating to your Content, your use of the Services or any willful misconduct on your part.</p>

                        <h4>Severability</h4>
                        <p>All rights and restrictions contained in this Agreement may be exercised and shall be applicable and binding only to the extent that they do not violate any applicable laws and are intended to be limited to the extent necessary so that they will not render this Agreement illegal, invalid or unenforceable. If any provision or portion of any provision of this Agreement shall be held to be illegal, invalid or unenforceable by a court of competent jurisdiction, it is the intention of the parties that the remaining provisions or portions thereof shall constitute their agreement with respect to the subject matter hereof, and all such remaining provisions or portions thereof shall remain in full force and effect.</p>

                        <h4>Dispute resolution</h4>
                        <p>The formation, interpretation, and performance of this Agreement and any disputes arising out of it shall be governed by the substantive and procedural laws of Washington, United States without regard to its rules on conflicts or choice of law and, to the extent applicable, the laws of United States. The exclusive jurisdiction and venue for actions related to the subject matter hereof shall be the courts located in Washington, United States, and you hereby submit to the personal jurisdiction of such courts. You hereby waive any right to a jury trial in any proceeding arising out of or related to this Agreement. The United Nations Convention on Contracts for the International Sale of Goods does not apply to this Agreement.</p>

                        <h4>Changes and amendments</h4>
                        <p>We reserve the right to modify this Agreement or its terms related to the Services at any time at our discretion. When we do, we will revise the updated date at the bottom of this page. We may also provide notice to you in other ways at our discretion, such as through the contact information you have provided.</p>
                        <br><p>An updated version of this Agreement will be effective immediately upon the posting of the revised Agreement unless otherwise specified. Your continued use of the Services after the effective date of the revised Agreement (or such other act specified at that time) will constitute your consent to those changes.</p>

                        <h4>Acceptance of these terms</h4>
                        <p>You acknowledge that you have read this Agreement and agree to all its terms and conditions. By accessing and using the Services you agree to be bound by this Agreement. If you do not agree to abide by the terms of this Agreement, you are not authorized to access or use the Services.</p>

                        <h4>Contacting us</h4>
                        <p>If you have any questions, concerns, or complaints regarding this Agreement, we encourage you to contact us using the details below:</p>

                        <p><strong><a href="{{ cache('brand_settings')?->website_url ?? '#' }}">{{ config('app.name') }}</a>.</strong></p>
                        <p><strong><a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a>.</strong></p>
                        <p>This document was last updated on February 11, 2025￼</p>
                </div>
        </div>
</div>
@endsection
