<?xml version='1.0'?>
<xsl:stylesheet
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
    xmlns:fo="http://www.w3.org/1999/XSL/Format">
<!--
 Customization xsl stylesheet for docbook to pdf transform
 @author Gaetano Giunta
 @copyright (c) 2007-2015 G. Giunta
 @license code licensed under the BSD License
 @todo make the xsl more dynamic: the path to import docbook.xsl could be f.e. rewritten/injected by the php user
-->


<!-- import base stylesheet -->
<xsl:import href="../../../../vendor/docbook/docbook-xsl/fo/docbook.xsl"/>


<!-- customization vars -->
<xsl:param name="fop1.extensions">1</xsl:param>
<xsl:param name="draft.mode">no</xsl:param>
<xsl:param name="funcsynopsis.style">ansi</xsl:param>
<xsl:param name="id.warnings">0</xsl:param>
<xsl:param name="highlight.source">1</xsl:param>
<xsl:param name="highlight.default.language">php</xsl:param>
<xsl:param name="paper.type">A4</xsl:param>
<xsl:param name="shade.verbatim">1</xsl:param>

<xsl:attribute-set name="verbatim.properties">
  <xsl:attribute name="font-size">80%</xsl:attribute>
</xsl:attribute-set>


<!-- elements added / modified -->
<xsl:template match="funcdef/function">
  <xsl:choose>
    <xsl:when test="$funcsynopsis.decoration != 0">
      <fo:inline font-weight="bold">
        <xsl:apply-templates/>
      </fo:inline>
    </xsl:when>
    <xsl:otherwise>
      <xsl:apply-templates/>
    </xsl:otherwise>
  </xsl:choose>
  <xsl:text> </xsl:text>
</xsl:template>

<xsl:template match="funcdef/type">
  <xsl:apply-templates/>
  <xsl:text> </xsl:text>
</xsl:template>

<xsl:template match="void">
  <xsl:choose>
    <xsl:when test="$funcsynopsis.style='ansi'">
      <xsl:text>( void )</xsl:text>
    </xsl:when>
    <xsl:otherwise>
      <xsl:text>( )</xsl:text>
    </xsl:otherwise>
  </xsl:choose>
</xsl:template>

<xsl:template match="varargs">
  <xsl:text>( ... )</xsl:text>
</xsl:template>

<xsl:template match="paramdef">
  <xsl:variable name="paramnum">
    <xsl:number count="paramdef" format="1"/>
  </xsl:variable>
  <xsl:if test="$paramnum=1">( </xsl:if>
  <xsl:choose>
    <xsl:when test="$funcsynopsis.style='ansi'">
      <xsl:apply-templates/>
    </xsl:when>
    <xsl:otherwise>
      <xsl:apply-templates select="./parameter"/>
    </xsl:otherwise>
  </xsl:choose>
  <xsl:choose>
    <xsl:when test="following-sibling::paramdef">
      <xsl:text>, </xsl:text>
    </xsl:when>
    <xsl:otherwise>
      <xsl:text> )</xsl:text>
    </xsl:otherwise>
  </xsl:choose>
</xsl:template>

<xsl:template match="paramdef/type">
  <xsl:apply-templates/>
  <xsl:text> </xsl:text>
</xsl:template>

<!-- default values for function parameters -->
<xsl:template match="paramdef/initializer">
  <xsl:text> = </xsl:text>
  <xsl:apply-templates/>
</xsl:template>


</xsl:stylesheet>