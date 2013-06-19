/**
 * Copyright (C) 2010 by Drupella.com - info at drupella dot com - http://drupella.com
 * Usage and redistribution of this javasript code requires permission from Drupella.com.
 */

var ajaxGMBuild=function(b,a,c){b.extend(a,{initiate:function(){if(c.gmap&&c.behaviors.GMap&&!c.behaviors.GMap.detach){c.behaviors.GMap.detach=function(d,e){b(".gmap-gmap",d).filter(".gmap-processed").each(function(g,h){var k,h,f=h.id.split("-");if(c.settings.gmap_remap_widgets&&c.settings.gmap_remap_widgets[h.id]){f=c.settings.gmap_remap_widgets[h.id].id.split("-")}f.pop();f.shift();f=f.join("-");if(h=c.gmap.getMap(f)){if(k=c.settings.gmap[f]){var j=h.map.getCenter();k.latitude=j.lat();k.longitude=j.lng();k.zoom=h.map.getZoom();k.maptype=h.opts.mapTypeNames[b.inArray(h.map.getCurrentMapType(),h.opts.mapTypes)]}c.gmap.unloadMap(f)}})}}}})};