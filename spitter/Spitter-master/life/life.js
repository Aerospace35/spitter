/*
 * Game of Life for HTML5.
 * Invented by John Horton Conway.
 *
 * Copyright © 2010, Daniel Keep.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

var canvas;
var width = 500;
var height = 500;
var scale = 4;
var bgColor = '#000000';
var fgColor = '#00FF33';
var stepInterval = 1/500;
var resetDensity = 0.2;
var resetInterval = 0;

var state = [];

function getParams()
{
    var params = {};
    var qs = location.search.substring(1, location.search.length);
    if( qs.length == 0 ) return params;

    qs = qs.replace(/\+/g, ' ');
    var qsp = qs.split('&');

    for( var i=0; i<qsp.length; ++i )
    {
        var p = qsp[i].split('=');
        var name = decodeURIComponent(p[0]);

        var value = true;
        if( p.length == 2 )
            value = decodeURIComponent(p[1]);

        params[name] = value;
    }

    return params;
}

function init()
{
    var params = getParams();
    width = Number(params['width']) || width;
    height = Number(params['height']) || height;
    scale = Number(params['scale']) || scale;
    bgColor = params['bgColor'] || bgColor;
    fgColor = params['fgColor'] || fgColor;
    stepInterval = params['stepInterval'] || stepInterval;
    resetDensity = params['resetDensity'] || resetDensity;
    resetInterval = params['resetInterval'] || resetInterval;

    document.body.style.setProperty("background-color", bgColor, null);

    canvas = document.getElementById('display');
    canvas.width = width*scale;
    canvas.height = height*scale;

    reset(resetDensity);

    setInterval(function() { draw(); step(); }, 1000*stepInterval);

    if( resetInterval > 0 )
        setInterval(function() { reset(resetDensity); }, 1000*resetInterval);
}

function reset(chance)
{
    state = [];
    for( var i=0; i<width*height; ++i )
        state[i] = (Math.random() <= chance);
}

function draw()
{
    var ctx = canvas.getContext('2d');

    ctx.fillStyle = bgColor;
    ctx.fillRect(0, 0, width*scale, height*scale);

    ctx.fillStyle = fgColor;

    for( var y=0; y<height; ++y )
    for( var x=0; x<width; ++x )
        if( state[x+y*width] )
            ctx.fillRect(x*scale, y*scale, scale, scale);
}

function step()
{
    function cell(x,y)
    {
        if( x < 0 || x >= width || y < 0 || y >= height )
            return false;
        else
            return state[x+y*width];
    }

    function rule(x,y)
    {
        var neighbours = (
                cell(x-1,y-1) + cell(x,y-1) + cell(x+1,y-1)
                + cell(x-1,y) + cell(x+1,y)
                + cell(x-1,y+1) + cell(x,y+1) + cell(x+1,y+1) );

        if( cell(x,y) )
            return 2 <= neighbours && neighbours <= 3;
        else
            return neighbours == 3;
    }

    var newState = [];

    for( var y=0; y<height; ++y )
    for( var x=0; x<width; ++x )
        newState[x+y*width] = rule(x,y);

    state = newState;
}

