@extends('layouts.app')

@section('meta_title', 'Our Heritage & Morjim Unit - Dev Can Fruit Products | PatSons Foods')
@section('meta_description', 'The story behind PatSons Foods and Dev Can Fruit Products in Morjim, Goa — three decades of making real Goan jams, syrups and kokum the slow way.')
@section('meta_keywords', 'Dev Can Fruit Products, PatSons Foods story, Vivek Laxman Patki, Morjim fruit processing, authentic Goan food heritage, Morjim factory Goa')
@section('canonical', route('about'))

@push('schema')
@php
    $aboutSchema = [
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
                        "name" => "Our Heritage & Morjim Unit",
                        "item" => route('about')
                    ]
                ]
            ],
            [
                "@type" => "AboutPage",
                "name" => "Our Heritage & Morjim Unit - Dev Can Fruit Products | PatSons Foods",
                "description" => "The story behind PatSons Foods and Dev Can Fruit Products in Morjim, Goa — three decades of making real Goan jams, syrups and kokum.",
                "url" => route('about'),
                "mainEntity" => [
                    "@type" => "Organization",
                    "name" => "Dev Can Fruit Products (PatSons Foods)",
                    "legalName" => "Dev Can Fruit Products",
                    "foundingDate" => "1990",
                    "founder" => [
                        "@type" => "Person",
                        "name" => "Vivek Laxman Patki"
                    ],
                    "hasMap" => "https://maps.google.com/?q=Dev+Can+Fruit+Products+Morjim+Goa",
                    "address" => [
                        "@type" => "PostalAddress",
                        "streetAddress" => "Dev Can Fruit Products, Morjim",
                        "addressLocality" => "Pernem",
                        "addressRegion" => "Goa",
                        "postalCode" => "403512",
                        "addressCountry" => "IN"
                    ]
                ]
            ]
        ]
    ];
@endphp
<script type="application/ld+json">
{!! json_encode($aboutSchema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-12 space-y-10">

        <!-- ==================== PAGE HEADER ==================== -->
        <div class="text-center">
            <span class="text-[10px] font-extrabold text-green-800 uppercase tracking-widest">About PatSons Foods</span>
            <h1 class="text-3xl sm:text-4xl font-black text-gray-900 mt-2">A Small Goan Kitchen That Never Stopped Cooking
            </h1>
            <p class="mt-3 text-xs sm:text-sm text-gray-500 max-w-xl mx-auto">
                Three decades in Morjim. Same recipes. Same hands. Same stubbornness about doing it right.
            </p>
        </div>

        <!-- ==================== FOUNDERS ==================== -->
        <div class="flex flex-col sm:flex-row items-center gap-5 bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">

            <div
                class="relative w-32 h-32 sm:w-40 sm:h-40 shrink-0 rounded-full overflow-hidden ring-4 ring-green-100 shadow-md bg-stone-200">
                <img src="{{ asset('images/products/founders.webp') }}" alt="Vivek Laxman Patki & family"
                    class="w-full h-full object-cover object-center">
            </div>

            <div class="text-center sm:text-left space-y-1">
                <h2 class="text-lg font-bold text-gray-900">Vivek Laxman Patki</h2>
                <p class="text-[11px] font-bold text-green-800 uppercase tracking-wider">Founder • Dev Can Fruit Products,
                    Morjim</p>
                <p class="text-xs text-gray-600 leading-relaxed pt-2">
                    Started it all in 1990 in a small Morjim unit. Still checks every batch himself.
                </p>
            </div>

        </div>

        <!-- ==================== OUR STORY & HERITAGE ==================== -->
        <div
            class="bg-white rounded-3xl p-6 sm:p-10 border border-gray-100 shadow-sm space-y-6 text-xs sm:text-sm text-gray-600 leading-relaxed">
            <h2 class="text-lg font-bold text-gray-900">How It All Started</h2>

            <p>
                Back in 1990, Vivek Patki started making fruit products in a small unit in Morjim with one simple
                belief — that if you use good fruit and don't rush it, people will taste the difference. Thirty-six
                years later, that belief hasn't changed.
            </p>

            <p>
                We still pick our fruit the way we always have. We still cook the jams slowly, in small batches, the
                way they're supposed to be cooked. We still sun-dry our kokum on the Goan coast. Nobody told us to do
                it this way — it's just how it's done here.
            </p>

            <p>
                What started as one man's kitchen has since become <strong>Dev Can Fruit Products</strong>. Today we
                supply families, shops, hotels, and distributors across India — but the person packing your bottle
                is still following the same recipe book from 1990. That part hasn't changed, and honestly, we don't
                want it to.
            </p>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 py-4">
                <div class="bg-green-50 p-4 rounded-2xl text-center">
                    <span class="text-2xl font-black text-green-800 block">36+</span>
                    <span class="text-[10px] font-bold text-green-900">Years of Doing This</span>
                </div>
                <div class="bg-green-50 p-4 rounded-2xl text-center">
                    <span class="text-2xl font-black text-green-800 block">100%</span>
                    <span class="text-[10px] font-bold text-green-900">Real Fruit Inside</span>
                </div>
                <div class="bg-green-50 p-4 rounded-2xl text-center">
                    <span class="text-2xl font-black text-green-800 block">Zero</span>
                    <span class="text-[10px] font-bold text-green-900">Shortcuts Taken</span>
                </div>
                <div class="bg-green-50 p-4 rounded-2xl text-center">
                    <span class="text-2xl font-black text-green-800 block">FSSAI</span>
                    <span class="text-[10px] font-bold text-green-900">Licensed & Tested</span>
                </div>
            </div>
        </div>

        <!-- ==================== MISSION / VISION / COMMITMENT ==================== -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">

            <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm space-y-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-900 border border-emerald-100 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-black text-gray-900">What We're Trying To Do</h3>
                <p class="text-xs text-gray-600 leading-relaxed">
                    Make fruit products the honest way — good for you, good for the farmers we buy from, and good
                    enough to serve at your own table. That's the whole goal.
                </p>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm space-y-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-900 border border-emerald-100 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-black text-gray-900">Where We're Headed</h3>
                <p class="text-xs text-gray-600 leading-relaxed">
                    To be the name people think of when they want real Goan kokum, real cashew jam, real fruit —
                    whether they're in Panaji, Pune, or Dubai. Slowly, steadily, one bottle at a time.
                </p>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm space-y-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-900 border border-emerald-100 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-black text-gray-900">Our Promise to You</h3>
                <p class="text-xs text-gray-600 leading-relaxed">
                    Every batch is checked before it leaves the factory. If it's not good enough for us to keep in our
                    own kitchen, it doesn't go out. That's been the rule since day one.
                </p>
            </div>

        </div>

        <!-- ==================== OUR PRODUCTS RANGE ==================== -->
        <div class="bg-white rounded-3xl p-6 sm:p-10 border border-gray-100 shadow-sm space-y-5">
            <div>
                <span class="text-[10px] font-extrabold text-green-800 uppercase tracking-widest">What We Make</span>
                <h2 class="text-lg font-bold text-gray-900 mt-1">The PatSons Range</h2>
                <p class="text-xs text-gray-500 mt-1">
                    A little bit of everything Goan — from the old favourites to the ones you haven't tried yet.
                </p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                <div class="flex items-center gap-2.5 bg-stone-50 rounded-xl px-3.5 py-2.5 border border-stone-200/60">
                    <span class="w-2 h-2 rounded-full bg-emerald-600 shrink-0"></span><span class="font-bold text-gray-800">Fruit Pulps</span>
                </div>
                <div class="flex items-center gap-2.5 bg-stone-50 rounded-xl px-3.5 py-2.5 border border-stone-200/60">
                    <span class="w-2 h-2 rounded-full bg-emerald-600 shrink-0"></span><span class="font-bold text-gray-800">Fruit Crushes</span>
                </div>
                <div class="flex items-center gap-2.5 bg-stone-50 rounded-xl px-3.5 py-2.5 border border-stone-200/60">
                    <span class="w-2 h-2 rounded-full bg-emerald-600 shrink-0"></span><span class="font-bold text-gray-800">Fruit Syrups</span>
                </div>
                <div class="flex items-center gap-2.5 bg-stone-50 rounded-xl px-3.5 py-2.5 border border-stone-200/60">
                    <span class="w-2 h-2 rounded-full bg-emerald-600 shrink-0"></span><span class="font-bold text-gray-800">Fruit Juices</span>
                </div>
                <div class="flex items-center gap-2.5 bg-stone-50 rounded-xl px-3.5 py-2.5 border border-stone-200/60">
                    <span class="w-2 h-2 rounded-full bg-emerald-600 shrink-0"></span><span class="font-bold text-gray-800">Jams</span>
                </div>
                <div class="flex items-center gap-2.5 bg-stone-50 rounded-xl px-3.5 py-2.5 border border-stone-200/60">
                    <span class="w-2 h-2 rounded-full bg-emerald-600 shrink-0"></span><span class="font-bold text-gray-800">Marmalades</span>
                </div>
                <div class="flex items-center gap-2.5 bg-stone-50 rounded-xl px-3.5 py-2.5 border border-stone-200/60">
                    <span class="w-2 h-2 rounded-full bg-emerald-600 shrink-0"></span><span class="font-bold text-gray-800">Cube Jelly Sweets</span>
                </div>
                <div class="flex items-center gap-2.5 bg-stone-50 rounded-xl px-3.5 py-2.5 border border-stone-200/60">
                    <span class="w-2 h-2 rounded-full bg-emerald-600 shrink-0"></span><span class="font-bold text-gray-800">Dry Kokum</span>
                </div>
                <div class="flex items-center gap-2.5 bg-stone-50 rounded-xl px-3.5 py-2.5 border border-stone-200/60">
                    <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span><span class="font-bold text-gray-800">And More…</span>
                </div>
            </div>
        </div>

        <!-- ==================== OFFICIAL CERTIFICATIONS ==================== -->
        <div
            class="bg-white rounded-3xl p-6 sm:p-10 border border-gray-100 shadow-sm space-y-4 text-xs sm:text-sm text-gray-600">
            <h2 class="text-lg font-bold text-gray-900">Where To Find Us / Official Details</h2>
            <ul class="list-disc pl-5 space-y-1.5 text-xs">
                <li><strong>Manufacturer:</strong> Dev Can Fruit Products</li>
                <li><strong>Factory Address:</strong> Morjim, Pedne, Goa — 403512, India</li>
                <li><strong>FSSAI Licence No:</strong> 10613001000730</li>
                <li><strong>GSTIN:</strong> 30BAGPP8577A1ZS</li>
                <li><strong>Call or WhatsApp:</strong> 9823464066</li>
                <li><strong>Email:</strong> vivekpatkigoa@gmail.com</li>
                <li><strong>Website:</strong> www.patsonsgoa.com</li>
            </ul>
            <p class="text-xs text-gray-500 italic pt-2">
                If you're ever near Morjim, drop by the factory. We'll gladly show you how the jams are made — and
                you'll probably leave with a bottle in your bag.
            </p>
        </div>

    </div>
@endsection