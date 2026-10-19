{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">

    {{-- ==================== Static Main Pages ==================== --}}

    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    <url>
        <loc>{{ url('/shop') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>

    <url>
        <loc>{{ url('/about') }}</loc>
        <lastmod>{{ now()->subDays(7)->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>

    <url>
        <loc>{{ url('/contact') }}</loc>
        <lastmod>{{ now()->subDays(7)->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>

    {{-- ==================== Product Category Pages ==================== --}}

    @foreach($categories as $category)
        <url>
            <loc>{{ url('/shop?category=' . urlencode($category)) }}</loc>
            <lastmod>{{ now()->toAtomString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>
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
                    <image:title>{{ htmlspecialchars($product->product_name, ENT_XML1, 'UTF-8') }}</image:title>
                </image:image>
            @endif
        </url>
    @endforeach

</urlset>