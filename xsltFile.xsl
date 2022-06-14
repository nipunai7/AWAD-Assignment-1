<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="/">
  <html>
  <head>
    <title>XML- Assignment 1</title>
  </head>
  <body>
  <h2>My CD Collection</h2>
    <xsl:variable name="title" select="title"/>
    <xsl:variable name="artist" select="artist"/>
    <xsl:variable name="country" select="country"/>
    <xsl:variable name="company" select="company"/>
    <xsl:variable name="price" select="price"/>
    <xsl:variable name="year" select="year"/>

    <form action="#">
      <table>
        <tr><td>Title:</td><td><input type="text" name="title" value="{$title}"></input></td></tr>
        <tr><td>Artist:</td><td><input type="text" name="artist" value="{$artist}"/></td></tr>
        <tr><td>Country:</td><td><input type="text" name="country" value="{$country}"/></td></tr>
        <tr><td>Company:</td><td><input type="text" name="company" value="{$company}"></input></td></tr>
        <tr><td>Price:</td><td><input type="text" name="price" value="{$price}"/></td></tr>
        <tr><td>Year:</td><td><input type="text" name="year" value="{$year}"/></td></tr>
        <tr><td></td><td><input type="submit" value="Submit" onclick="test()"/></td></tr>
      </table>
    </form>
  <table border="1">
    <tr bgcolor="#9acd32">
      <th>Title</th>
      <th>Artist</th>
      <th>Country</th>
      <th>Company</th>
      <th>Price</th>
      <th>Year</th>
    </tr>
    <xsl:for-each select="catalog/cd">
    <tr>
      <td><xsl:value-of select="title"/></td>
      <td><xsl:value-of select="artist"/></td>
      <td><xsl:value-of select="country"/></td>
      <td><xsl:value-of select="company"/></td>
      <td><xsl:value-of select="price"/></td>
      <td><xsl:value-of select="year"/></td>
    </tr>
    </xsl:for-each>
  </table>
  <script>
    function test(){
       xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            myFunction(xhttp);
        };

        xhttp.open("GET", "xmlFile.xml", true);
        xhttp.send();

        xmlDoc = xhttp.responseXML;

      alert("shit im working");
      newNode = xmlDoc.createElement("cd");

      x = xmlDoc.documentElement;
      y = xmlDoc.getElementsByTagName("cd")[0];

      x.insertBefore(newNode,y);
    }
  </script>
  </body>
  </html>
</xsl:template>

</xsl:stylesheet>