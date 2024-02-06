<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="/">
        <html>
            <head>
                <title>Weather History</title>
                <style>
                    table {
                    border-collapse: collapse;
                    width: 100%;
                    }
                    th, td {
                    border: 1px solid #dddddd;
                    text-align: left;
                    padding: 8px;
                    }
                    th {
                    background-color: #f2f2f2;
                    }
                </style>
            </head>
            <body>
                <h2>Saved Weather</h2>
                <table>
                    <tr>
                        <th>Location</th>
                        <th>Date</th>
                        <th>Weather</th>
                        <th>Max Temp (°C)</th>
                        <th>Min Temp (°C)</th>
                        <th>Sunrise (GMT +0)</th>
                        <th>Sunset (GMT +0)</th>
                    </tr>
                    <xsl:for-each select="history/record">
                        <tr>
                            <td><xsl:value-of select="location"/></td>
                            <td><xsl:value-of select="date"/></td>
                            <td><xsl:value-of select="weather"/></td>
                            <td><xsl:value-of select="maxTemp"/></td>
                            <td><xsl:value-of select="minTemp"/></td>
                            <td><xsl:value-of select="sunrise"/></td>
                            <td><xsl:value-of select="sunset"/></td>
                        </tr>
                    </xsl:for-each>
                </table>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
