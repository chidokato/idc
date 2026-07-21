<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
    <url>
        <loc>{{asset('')}}</loc>
        <changefreq>always</changefreq>
        <priority>1</priority>
    </url>
    
    @foreach ($category as $val)
    <url>
        <loc>{{asset('')}}{{$val->slug}}</loc>
        <changefreq>always</changefreq>
        <priority>{{ $val->sort_by == 'Product' ? "0.9" : "0.7" }}</priority>
    </url>
    @endforeach

    @foreach ($Post as $val)
    <url>
        <loc>{{asset('')}}{{$val->category->slug}}/{{$val->slug}}</loc>
        <changefreq>always</changefreq>
        <priority>{{ $val->sort_by == 'Product' ? "0.9" : "0.7" }}</priority>
    </url>
    @endforeach
	
</urlset>