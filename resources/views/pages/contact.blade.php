@extends('layouts.app')

@section('meta_title', 'Contact Us & Factory Inquiries | PatSons Foods Goa')
@section('meta_description', 'Get in touch with PatSons Foods factory at Morjim, Pernem, Goa for wholesale orders, distribution partnerships, and bulk product quotations.')
@section('meta_keywords', 'contact PatSons Foods, Dev Can Fruit Products Morjim, PatSons Goa phone number, Goan food wholesale contact, Morjim factory Goa')
@section('canonical', route('contact'))

@push('schema')
@php
    $contactSchema = [
        "@context" => "https://schema.org",
        "@graph" => [
            [
                "@type" => "BreadcrumbList",
                "itemListElement" => [
                    [
                        "@type" => "ListItem",
                        "position" => 1,
                        "name" => "Home",
                        "item" => route('home')
                    ],
                    [
                        "@type" => "ListItem",
                        "position" => 2,
                        "name" => "Contact Us",
                        "item" => route('contact')
                    ]
                ]
            ],
            [
                "@type" => "ContactPage",
                "name" => "Contact Us & Factory Inquiries | PatSons Foods Goa",
                "description" => "Contact Dev Can Fruit Products and PatSons Foods factory in Morjim, Pernem, Goa.",
                "url" => route('contact'),
                "mainEntity" => [
                    "@type" => "LocalBusiness",
                    "name" => "PatSons Foods (Dev Can Fruit Products)",
                    "legalName" => "Dev Can Fruit Products",
                    "telephone" => "+919823464066",
                    "email" => "patsons1993@rediffmail.com",
                    "hasMap" => "https://maps.google.com/?q=Dev+Can+Fruit+Products+Morjim+Pernem+Goa",
                    "address" => [
                        "@type" => "PostalAddress",
                        "streetAddress" => "Dev Can Fruit Products, Morjim",
                        "addressLocality" => "Pernem",
                        "addressRegion" => "Goa",
                        "postalCode" => "403512",
                        "addressCountry" => "IN"
                    ],
                    "geo" => [
                        "@type" => "GeoCoordinates",
                        "latitude" => "15.6322",
                        "longitude" => "73.7438"
                    ]
                ]
            ]
        ]
    ];
@endphp
<script type="application/ld+json">
{!! json_encode($contactSchema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-12">
        <div class="text-center mb-10">
            <span class="text-[10px] font-extrabold text-green-800 uppercase tracking-widest">Get In Touch</span>
            <h1 class="text-3xl font-black text-gray-900 mt-2">Contact PatSons Foods Goa</h1>
            <p class="text-xs text-gray-500 mt-2">For retail inquiries, distributor orders, or direct delivery support across Goa and India.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-4">
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-2xs">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-900 flex items-center justify-center mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-sm text-gray-900 mt-1">Factory & Registered Office</h3>
                    <p class="text-xs text-gray-500 mt-1">Dev Can Fruit Products<br>Morjim, Pernem, Goa - 403512, India</p>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-2xs">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-900 flex items-center justify-center mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-sm text-gray-900 mt-1">Customer Care & WhatsApp</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        Direct Support: <a href="tel:9823464066"
                            class="text-green-800 font-bold hover:underline">+91 9823464066</a>
                    </p>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-2xs">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-900 flex items-center justify-center mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-sm text-gray-900 mt-1">Email Queries</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        <a href="mailto:patsons1993@rediffmail.com"
                            class="text-green-800 font-semibold hover:underline">patsons1993@rediffmail.com</a>
                    </p>
                </div>
            </div>

            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-gray-100 shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="font-bold text-base text-gray-900">Direct Inquiries</h3>
                    <p class="text-xs text-gray-500 mt-1 mb-4">Have questions about bulk orders or trade supply? Chat with
                        our team instantly on WhatsApp.</p>
                    <div class="bg-green-50 p-4 rounded-xl text-xs text-green-900 space-y-1.5 mb-6 font-medium">
                        <p class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-emerald-700 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Real-time stock status</span>
                        </p>
                        <p class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-emerald-700 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Custom packing & wholesale rates</span>
                        </p>
                        <p class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-emerald-700 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Direct Goa delivery confirmation</span>
                        </p>
                    </div>
                </div>
                <a href="https://wa.me/919823464066?text=Hello%20PatSons%20Goa!%20I%20have%20an%20inquiry%20regarding%20your%20products."
                    target="_blank"
                    class="w-full bg-green-700 hover:bg-green-800 text-white font-bold py-3 rounded-xl text-xs text-center flex items-center justify-center gap-2 shadow-sm transition">
                    <span>Chat on WhatsApp →</span>
                </a>
            </div>
        </div>
    </div>
@endsection