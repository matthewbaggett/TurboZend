<?xml version="1.0" encoding="utf-8"?>

<xsl:stylesheet version="1.0"    
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    exclude-result-prefixes="xsl">

<xsl:output 
		omit-xml-declaration="yes"
		indent="yes" />

<xsl:template match="title">
	<!-- <h1><xsl:value-of select="."/><xsl:text> </xsl:text></h1> -->
</xsl:template>

<xsl:template match="cblocks">
	<xsl:apply-templates select="cblock"/>
</xsl:template>

<xsl:template match="cblock">
	<div class="cblock">
		<div class="heading"><ul><li>Cblock</li></ul></div>
		<xsl:apply-templates select="gblock"/>
		<xsl:text> </xsl:text>
	</div>
</xsl:template>

<xsl:template match="gblock">
	<div class="gblock">
		<div class="heading">
			<ul>
				<li>Gblock</li>
				<li class="right new-above">New Above</li>
				<li class="right new-below">New Below</li>
				<li class="right delete">Delete</li>
			</ul>
		</div>
		<div class="inner">
			<h3 class="options">Options >></h3>
			<dl class="options">
				<dt>Width:</dt>
				<dd><input name="width" value="{@width}"/></dd>
				<dt>ID:</dt>
				<dd><input name="id" value="{@id}"/></dd>
				<dt>Extra Classes:</dt>
				<dd><input name="extra_classes" value="{@classes}"/></dd>
			</dl>
			
			<xsl:apply-templates select="heading|subheading|text" mode="gblock-inner"/>
		</div>
	</div>
</xsl:template>

<xsl:template mode="gblock-inner" match="heading">
		<div class="formrow">
			<div class="heading">
				<ul>
					<li>Heading</li>
					<li class="right delete">Delete</li>
				</ul>
			</div>
			<label for="width">Heading:</label><input name="heading" value="{.}"/>
		</div>
</xsl:template>

<xsl:template mode="gblock-inner" match="subheading">
		<div class="formrow">
			<div class="heading">
				<ul>
					<li>Subheading</li>
					<li class="right delete">Delete</li>
				</ul>
			</div>
			<label for="width">Subheading:</label><input name="heading" value="{.}"/>
		</div>
</xsl:template>

<xsl:template mode="gblock-inner" match="tileblock">
	<xsl:apply-templates select="tiles/tile"/>
	<xsl:text> </xsl:text>
</xsl:template>

<xsl:template mode="gblock-inner" match="links">
	<ul class=" {@classes}" id="{@id}">
		<xsl:for-each select="link">
			<li><a href=".@url"><xsl:value-of select="."/><xsl:text> </xsl:text></a></li>
		</xsl:for-each>
	</ul>
</xsl:template>

<xsl:template mode="gblock-inner" match="carousel">
<!-- Slide Show-->
	<div id="slider-ribbon"><xsl:text> </xsl:text></div>
	<div id="slider">
		<div id="slide-backs"><xsl:text> </xsl:text></div>
		<div id="slides">
			<div class="slides_container">
				<xsl:for-each select="slides/slide">
					<a href="{link}">
						<img src="{image}" alt="{alt}"  />
					</a>
				</xsl:for-each>
				<xsl:text> </xsl:text>
			</div>
		</div>
	</div>
</xsl:template>

<xsl:template match="tile">
	<div class="grid_4 mini-advert" style="margin-left:2px;">
       <div id="image-hover">
          <a href="#">
             <img width="220" height="110" alt="" src="image/theme/spacer.png"/>
          </a>
       </div>
       <img alt="" src="{image}"/>
       <h1><xsl:value-of select="heading"/><xsl:text> </xsl:text></h1>
       [MARKDOWN]<xsl:text> </xsl:text><xsl:value-of select="text"/>[/MARKDOWN]
       <xsl:apply-templates select="link" mode="tile-link"/>
       
    </div>
</xsl:template>

<xsl:template match="link" mode="tile-link">
	<a style=" margin-left:5px;" class="grey-button" href="{url}">
          <div class="grey-right"><xsl:text> </xsl:text></div>
          <xsl:choose>
          	<xsl:when test="icon != ''">
          	   <img class="button-icon" alt="" src="image/theme/{icon}"/>
          	</xsl:when>
          	<xsl:otherwise>
          	   <img class="button-icon" alt="" src="image/theme/icon3.png"/>
          	</xsl:otherwise>
          </xsl:choose>
          <xsl:text> </xsl:text>
          <xsl:value-of select="label"/>
    </a>
</xsl:template>

<xsl:template match="text" mode="gblock-inner">
	<div class="formrow">
		<div class="heading">
			<ul>
				<li>Text</li>
				<li class="right delete">Delete</li>
			</ul>
		</div>
		<label for="mode">Mode:</label><input name="mode" value="{@mode}"/>
		<label for="text">Text:</label><textarea name="text"><xsl:copy-of select="."/></textarea>
	</div>
</xsl:template>

<xsl:template match="image">
	<img src="{.}" style="float: left; display: block;"/>
</xsl:template>

<xsl:template match="header">
	<h2><xsl:value-of select="."/><xsl:text> </xsl:text></h2>
</xsl:template>

</xsl:stylesheet>