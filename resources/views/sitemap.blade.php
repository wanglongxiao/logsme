<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<?php $day =  date('Y-m-d'); ?>
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>http://{{ env("DOMAINNAME") }}/</loc>
    <lastmod>{{ $day }}</lastmod>
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
  </url>
  @foreach (Config::get("weixin.tags") as $key => $val)
  <url>
    <loc>http://{{ env("DOMAINNAME") }}/tag/{{ $key }}</loc>
    <lastmod>{{ $day }}</lastmod>
    <changefreq>daily</changefreq>
    <priority>0.8</priority>
  </url>
  @endforeach
</urlset>