<SCRIPT LANGUAGE="JavaScript">
    // Set the BaseURL to the URL of your camera
    var BaseURL = "http://loshansen.axiscam.net/";
    // DisplayWidth & DisplayHeight specifies the displayed width & height of the image.
    // You may change these numbers, the effect will be a stretched or a shrunk image
    var DisplayWidth = "320";
    var DisplayHeight = "240";
    // This is the path to the image generating file inside the camera itself
    var File = "axis-cgi/mjpg/video.cgi?resolution=320x240";
    // No changes required below this point
    var output = "";
    if ((navigator.appName == "Microsoft Internet Explorer") &&
        (navigator.platform != "MacPPC") && (navigator.platform != "Mac68k")) {
// If Internet Explorer under Windows then use ActiveX
        output = '<OBJECT ID="Player" width='
        output += DisplayWidth;
        output += ' height=';
        output += DisplayHeight;
        output += ' CLASSID="CLSID:DE625294-70E6-45ED-B895-CFFA13AEB044" ';
        output += 'CODEBASE="';
        output += BaseURL;
        output += 'activex/AMC.cab#version=5,3,21,0">';
        output += '<PARAM NAME="MediaURL" VALUE="';
        output += BaseURL;
        output += File + '">';
        output += '<param name="MediaType" value="mjpeg">';
        output += '<param name="ShowStatusBar" value="0">';
        output += '<param name="ShowToolbar" value="0">';
        output += '<param name="AutoStart" value="1">';
        output += '<param name="StretchToFit" value="1">';
        output += '<BR><B>Axis Media Control</B><BR>';
        output += 'The AXIS Media Control, which enables you ';
        output += 'to view live image streams in Microsoft Internet';
        output += ' Explorer, could not be registered on your computer.';
        output += '<BR></OBJECT>';
    } else {
// If not IE for Windows use the browser itself to display
        theDate = new Date();
        output = '<IMG SRC="';
        output += BaseURL;
        output += File;
        output += '&dummy=' + theDate.getTime().toString(10);
        output += '" HEIGHT="';
        output += DisplayHeight;
        output += '" WIDTH="';
        output += DisplayWidth;
        output += '" ALT="Camera Image">';
    }
    document.write(output);
    document.Player.ToolbarConfiguration = "play,+snapshot,+fullscreen"
    // Remove the // below to use the code for Motion Detection.
    // document.Player.UIMode = "MDConfig";
    // document.Player.MotionConfigURL = "/axis-cgi/operator/param.cgi?ImageSource=0"
    // document.Player.MotionDataURL = "/axis-cgi/motion/motiondata.cgi";
</SCRIPT>