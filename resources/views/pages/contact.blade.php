@extends('layouts.app')

@section('meta_title', 'Contact Us & Factory Inquiries | PatSons Foods Goa')
@section('meta_description', 'Get in touch with PatSons Foods factory at Morjim, Pernem, Goa for wholesale orders, distribution partnerships, and bulk product quotations.')


@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-12">
        <div class="text-center mb-10">
            <span class="text-[10px] font-extrabold text-green-800 uppercase tracking-widest">Get In Touch</span>
            <h1 class="text-3xl font-black text-gray-900 mt-2">Contact PatSons Foods Goa</h1>
            <p class="text-xs text-gray-500 mt-2">For retail inquiries, distributor orders, or direct delivery support[cite:
                843].</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-4">
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-2xs">
                    <span class="text-lg">📍</span>
                    <h3 class="font-bold text-sm text-gray-900 mt-1">Factory & Registered Office</h3>
                    <p class="text-xs text-gray-500 mt-1">Dev Can Fruit Products<br>Morji, Pernem, Goa - 403512, India
                        [cite: 34, 35, 848, 849, 860]</p>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-2xs">
                    <span class="text-lg">📞</span>
                    <h3 class="font-bold text-sm text-gray-900 mt-1">Customer Care & WhatsApp</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        Direct Support: <a href="tel:9823464066"
                            class="text-green-800 font-bold hover:underline">9823464066</a> [cite: 37]
                    </p>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-2xs">
                    <span class="text-lg">✉️</span>
                    <h3 class="font-bold text-sm text-gray-900 mt-1">Email Queries</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        <a href="mailto:patsons1993@rediffmail.com"
                            class="text-green-800 font-semibold hover:underline">patsons1993@rediffmail.com</a> [cite: 38]
                    </p>
                </div>
            </div>

            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-gray-100 shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="font-bold text-base text-gray-900">Direct Inquiries</h3>
                    <p class="text-xs text-gray-500 mt-1 mb-4">Have questions about bulk orders or trade supply? Chat with
                        our team instantly on WhatsApp.</p>
                    <div class="bg-green-50 p-4 rounded-xl text-xs text-green-900 space-y-1 mb-6">
                        <p>✔ Real-time stock status</p>
                        <p>✔ Custom packing & wholesale rates</p>
                        <p>✔ Direct Goa delivery confirmation</p>
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