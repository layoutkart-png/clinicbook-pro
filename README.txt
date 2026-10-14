=== ClinicBook Pro ===
Contributors: ClinicBook Pro Team
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Doctor appointment booking theme for clinics — doctor listings with search
and filters, online slot booking, patient appointment lookup, and doctor
dashboards. Bilingual English/Hindi ready, INR pricing throughout.

== Description ==

ClinicBook Pro is an original, lightweight appointment-booking theme built
for single clinics in India:

* Doctors custom post type with specialization taxonomy
* Per-doctor weekly schedule (morning/evening ranges + slot duration)
* Real-time slot generation with double-booking protection
* 4-step booking wizard — no login required (guest-friendly)
* Appointments stored as a private CPT with pending/confirmed/cancelled status
* WhatsApp confirmation button (wa.me link with prefilled Hindi message)
* [clinicbook_my_appointments] — patients look up bookings by mobile number
* [clinicbook_doctor_dashboard] — logged-in staff confirm/cancel appointments
* Doctor profile pages with tabs (Overview / Reviews), star ratings, fees in ₹
* Find Doctors archive with keyword + specialization filters
* Customizer: clinic phone, timing, address, WhatsApp number, hero text, accent color
* Hindi translation shipped (languages/clinicbook-pro-hi.mo)

== Installation ==

1. In wp-admin go to Appearance → Themes → Add New → Upload Theme.
2. Upload clinicbook-pro.zip and Activate.
3. (Optional) Install a forms/SEO plugin of your choice — none required.
4. Go to Appearance → Customize → Clinic Settings: set phone, timing,
   address, WhatsApp number (digits with country code, e.g. 919876543210).
5. Create a page titled "Book Appointment", set its Template to "Booking",
   and publish (permalink /book-appointment/ recommended).
6. Create a page "My Appointments" with the [clinicbook_my_appointments]
   shortcode.
7. Create a page "Doctor Dashboard" with the [clinicbook_doctor_dashboard]
   shortcode (restrict to staff via a membership/role plugin if needed).
8. Set a static homepage (Settings → Reading) or leave the theme's
   front-page.php as the site front page.

== Demo content setup ==

1. Doctors → Add New for each doctor:
   - Featured image = doctor photo (480×520 recommended)
   - Assign a Specialization term (create terms like Cardiology, Dental…)
   - Fill "Doctor Details": fees in ₹, experience, credentials, clinic,
     location, phone, languages, education, services, consultation types,
     rating, patients treated, featured flag
   - Fill "Weekly Schedule": morning/evening from–to per day + slot minutes.
     Leave a day blank for a weekly off.
2. Menus: Appearance → Menus — create Primary (Home, Doctors, Book
   Appointment, Blog, Contact) and Footer menus.

== Shortcodes ==

* [clinicbook_doctors count="4" specialization="cardiology" featured="1"]
  — doctor cards grid.
* [clinicbook_my_appointments] — phone-number appointment lookup.
* [clinicbook_doctor_dashboard] — staff table with confirm/cancel actions.

== Hindi translation ==

The theme loads the 'clinicbook-pro' text domain. Hindi translations ship
in languages/clinicbook-pro-hi.mo (compiled from the .po without gettext,
verified by round-trip read). To use Hindi: install a multilingual plugin
(e.g. Polylang) or set WPLANG to hi_IN. To add another language, copy the
.po file, translate, and compile to .mo with msgfmt.

== INR notes ==

* All fees render with ₹ via cbp_format_inr().
* No payment gateway is bundled — payment is handled at the clinic.
  Wire Razorpay/UPI via the WhatsApp confirmation flow or a payments plugin.

== Changelog ==

1.0.0 — Initial release.

------------------------------------------------------------
PHARMACY / MEDICAL STORE FEATURES (v1.1.0)
------------------------------------------------------------
The theme now includes full online-pharmacy support (requires the free
WooCommerce plugin):

1. WooCommerce support is declared in functions.php (product gallery
   zoom, lightbox and slider included).

2. MEDICINE META FIELDS — edit any WooCommerce product and fill the
   "Medicine Details" box:
   - Generic Name, Company / Manufacturer, Form (Tablet/Syrup…),
     Strength (e.g. 500mg), Pack Size (e.g. 15 tablets)
   - "Prescription required (Rx)" checkbox

3. RX BADGE — a red "Rx" mark appears automatically on prescription
   products in the shop loop and on single product pages.

4. MEDICINE FACTS CARD — on the single product page, a facts table
   shows Generic Name, Company, Form, Strength and Pack Size, plus a
   medical disclaimer note.

5. WHATSAPP ORDERING — an "Order on WhatsApp" button appears on
   single product pages. It uses the number from Customizer → Clinic
   Settings → "Pharmacy WhatsApp Number" (falls back to the clinic
   WhatsApp number). The prefilled message includes the product name
   and price. On Rx products the custom "Prescription (Rx) Info Text"
   is shown under the button.

6. SHORTCODE [clinicbook_medicine_search] — medicine search bar with
   symptom chips (Fever, Cold, Pain, Acidity, Diabetes, BP, Vitamins,
   Baby Care) that filter the shop. Used on the homepage pharmacy band.

7. HOMEPAGE PHARMACY BAND — "Order Medicines Online" section after
   Featured Doctors: headline, symptom-chip search and a "Send
   Prescription on WhatsApp" button.

All pharmacy strings are translatable ('clinicbook-pro' domain) and
Hindi translations ship in languages/clinicbook-pro-hi.mo.
