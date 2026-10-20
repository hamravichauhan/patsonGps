{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">

    {{-- ==================== Static Main Pages ==================== --}}

    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
        <image:image>
            <image:loc>{{ asset('images/banner.webp') }}</image:loc>
            <image:title>PatSons Foods Goa - Authentic Goan Kokum Agal, Cashew Jams and Syrups</image:title>
            <image:caption>Dev Can Fruit Products factory banner in Morjim, Pernem - Goa</image:caption>
        </image:image>
    </url>

    <url>
        <loc>{{ url('/shop') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>

    <url>
        <loc>{{ url('/about') }}</loc>
        <lastmod>{{ now()->subDays(3)->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
        <image:image>
            <image:loc>{{ asset('images/logo1.webp') }}</image:loc>
            <image:title>Dev Can Fruit Products Morjim Factory Heritage</image:title>
            <image:caption>PatSons Foods manufacturing unit in Morjim, Goa since 1990</image:caption>
        </image:image>
    </url>

    <url>
        <loc>{{ url('/contact') }}</loc>
        <lastmod>{{ now()->subDays(3)->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>

    {{-- ==================== Product Category Pages ==================== --}}

    @foreach($categories as $category)
        <url>
            <loc>{{ url('/shop?category=' . urlencode($category)) }}</loc>
            <lastmod>{{ now()->toAtomString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.85</priority>
        </url>
    @endforeach

    {{-- ==================== Dynamic Product Pages ==================== --}}

    @foreach($products as $product)
        <url>
            <loc>{{ url('/product/' . $product->slug) }}</loc>
            <lastmod>{{ !empty($product->created_at) ? \Carbon\Carbon::parse($product->created_at)->toAtomString() : now()->toAtomString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>

            @if(!empty($product->image) && $product->image !== 'default-product.webp')
                <image:image>
                    <image:loc>{{ asset('images/products/' . $product->image) }}</image:loc>
                    <image:title>{{ htmlspecialchars($product->product_name . ' - Authentic Goan Product', ENT_XML1, 'UTF-8') }}</image:title>
                    <image:caption>{{ htmlspecialchars('Wholesale carton pack of ' . $product->product_name . ' from Dev Can Fruit Products, Morjim Goa', ENT_XML1, 'UTF-8') }}</image:caption>
                </image:image>
            @endif

            @if(!empty($product->images) && is_array($product->images))
                @foreach($product->images as $extraImg)
                    @if($extraImg !== $product->image && $extraImg !== 'default-product.webp')
                        <image:image>
                            <image:loc>{{ asset('images/products/' . $extraImg) }}</image:loc>
                            <image:title>{{ htmlspecialchars($product->product_name . ' packaging', ENT_XML1, 'UTF-8') }}</image:title>
                        </image:image>
                    @endif
                @endforeach
            @endif
        </url>
    @endforeach

</urlset>