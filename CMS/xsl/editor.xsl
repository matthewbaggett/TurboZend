<?xml version="1.0" encoding="utf-8"?>

<xsl:stylesheet version="1.0"    
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    exclude-result-prefixes="xsl">

<xsl:output 
		omit-xml-declaration="yes"
		indent="yes" />

<xsl:include href="../library/Turbo/CMS/xsl/header.xsl"/>
		
		
<!-- xml tempate -->
<xsl:template match="/">
		<xsl:apply-templates select="XML"/>
		<xsl:text> </xsl:text>
</xsl:template>

<xsl:template match="XML">
	<xsl:apply-templates select="*"/>
	<xsl:text> </xsl:text>
</xsl:template>

		
</xsl:stylesheet>