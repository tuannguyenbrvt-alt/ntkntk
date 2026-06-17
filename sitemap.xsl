<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:s="http://www.sitemaps.org/schemas/sitemap/0.9" exclude-result-prefixes="s">
    <xsl:output method="html" encoding="UTF-8" indent="yes"/>
    <xsl:template match="/">
        <html lang="vi">
            <head>
                <title>Sitemap XML - Trung Tâm Ngoại Ngữ Tin Học Nguyễn Minh</title>
                <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
                <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
                <style>
                    body {
                        font-family: 'Inter', sans-serif;
                        background-color: #f4f6f9;
                        color: #333;
                        margin: 0;
                        padding: 40px 20px;
                    }
                    .container {
                        max-width: 1000px;
                        margin: 0 auto;
                        background: #fff;
                        padding: 30px;
                        border-radius: 12px;
                        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
                        border-top: 4px solid #c9a84c;
                    }
                    .header {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        border-bottom: 2px solid #eaeaea;
                        padding-bottom: 20px;
                        margin-bottom: 20px;
                    }
                    .title {
                        margin: 0;
                        color: #111;
                        font-size: 24px;
                        font-weight: 700;
                    }
                    .title span {
                        color: #c9a84c;
                    }
                    .back-link {
                        color: #c9a84c;
                        text-decoration: none;
                        font-weight: 600;
                        font-size: 14px;
                        transition: color 0.2s;
                    }
                    .back-link:hover {
                        color: #a38234;
                    }
                    .desc {
                        color: #666;
                        font-size: 14px;
                        line-height: 1.6;
                        margin-bottom: 20px;
                    }
                    .stats {
                        background: #fafafa;
                        border: 1px solid #eee;
                        padding: 15px;
                        border-radius: 8px;
                        margin-bottom: 25px;
                        font-size: 14px;
                    }
                    .stats strong {
                        color: #c9a84c;
                        font-size: 16px;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-top: 10px;
                    }
                    th {
                        background-color: #0d1117;
                        color: #fff;
                        text-align: left;
                        padding: 12px 15px;
                        font-weight: 600;
                        font-size: 14px;
                    }
                    th:first-child {
                        border-radius: 6px 0 0 0;
                    }
                    th:last-child {
                        border-radius: 0 6px 0 0;
                    }
                    td {
                        padding: 12px 15px;
                        border-bottom: 1px solid #eee;
                        font-size: 14px;
                        word-break: break-all;
                    }
                    tr:hover td {
                        background-color: #f9f9f9;
                    }
                    .url-link {
                        color: #0d6efd;
                        text-decoration: none;
                        font-weight: 500;
                    }
                    .url-link:hover {
                        text-decoration: underline;
                    }
                    .priority-badge {
                        display: inline-block;
                        padding: 3px 8px;
                        border-radius: 12px;
                        font-size: 12px;
                        font-weight: 600;
                        background-color: #e3f2fd;
                        color: #0d6efd;
                    }
                    .priority-high {
                        background-color: #e8f5e9;
                        color: #2e7d32;
                    }
                    .priority-highest {
                        background-color: #fff3e0;
                        color: #e65100;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h1 class="title">XML <span>Sitemap</span></h1>
                        <a href="/" class="back-link">← Quay lại trang chủ</a>
                    </div>
                    
                    <p class="desc">Sitemap này được tạo tự động để giúp các công cụ tìm kiếm như Google, Bing lập chỉ mục các đường dẫn trên website <strong>Trung Tâm Ngoại Ngữ Tin Học Nguyễn Minh</strong> một cách chính xác.</p>
                    
                    <div class="stats">
                        Tổng số lượng liên kết được tìm thấy: <strong><xsl:value-of select="count(s:urlset/s:url)"/></strong>
                    </div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th width="55%">Đường dẫn (URL)</th>
                                <th width="15%">Định kỳ cập nhật</th>
                                <th width="15%">Độ ưu tiên (Priority)</th>
                                <th width="15%">Cập nhật cuối (Lastmod)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <xsl:for-each select="s:urlset/s:url">
                                <xsl:sort select="s:priority" order="descending"/>
                                <tr>
                                    <td>
                                        <a href="{s:loc}" class="url-link" target="_blank">
                                            <xsl:value-of select="s:loc"/>
                                        </a>
                                    </td>
                                    <td>
                                        <xsl:value-of select="s:changefreq"/>
                                    </td>
                                    <td>
                                        <span class="priority-badge">
                                            <xsl:attribute name="class">
                                                <xsl:choose>
                                                    <xsl:when test="s:priority = '1.0'">priority-badge priority-highest</xsl:when>
                                                    <xsl:when test="s:priority &gt;= '0.8'">priority-badge priority-high</xsl:when>
                                                    <xsl:otherwise>priority-badge</xsl:otherwise>
                                                </xsl:choose>
                                            </xsl:attribute>
                                            <xsl:value-of select="s:priority"/>
                                        </span>
                                    </td>
                                    <td>
                                        <xsl:choose>
                                            <xsl:when test="s:lastmod">
                                                <xsl:value-of select="s:lastmod"/>
                                            </xsl:when>
                                            <xsl:otherwise>-</xsl:otherwise>
                                        </xsl:choose>
                                    </td>
                                </tr>
                            </xsl:for-each>
                        </tbody>
                    </table>
                </div>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
