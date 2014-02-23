{% extends 'admin.base.volt' %}

{% block styles %}
    <link rel="stylesheet" href="/assets/admin/css/bootstrap/docs.css" type="text/css" media="screen" />
{% endblock %}

{% block content %}
<div id="pad-wrapper">
    <div class="col-md-10">
        <div class="page-header">
          <h1 id="type">Instructions</h1>
        </div>

        <!-- Headings -->
        <h3>Register a domain</h3>
        <p>
            When your site starts sending us clicks, we want to make sure it actually has
            the right to do so. Tell us on what domain you want to use Infrared on the
            <a href="/domains/new">New domain</a> page.
        </p>

        <h3>Add a bit of JavaScript to your page</h3>
        <p>
            Add the following bit of javascript <em>as is</em> to your page, just before the
            <code>&lt;/body&gt;</code> closing tag:
        </p>
            <!-- HTML generated using hilite.me --><div style="background: #272822; overflow:auto;width:auto;padding:.2em .6em;"><pre style="border:none;background: #272822;margin: 0; line-height: 125%"><span style="color: #f92672">&lt;script </span><span style="color: #a6e22e">type=</span><span style="color: #e6db74">&quot;text/javascript&quot;</span> <span style="color: #a6e22e">src=</span><span style="color: #e6db74">&quot;{{ scheme }}://{{ host }}/plugin/infrared.js&quot;</span><span style="color: #f92672">&gt;&lt;/script&gt;</span>
<span style="color: #f92672">&lt;script </span><span style="color: #a6e22e">type=</span><span style="color: #e6db74">&quot;text/javascript&quot;</span> <span style="color: #a6e22e">id=</span><span style="color: #e6db74">&quot;{{ user.getConfirmationCode() }}&quot;</span><span style="color: #f92672">&gt;</span>
    <span style="color: #a6e22e">Infrared</span><span style="color: #f8f8f2">.</span><span style="color: #a6e22e">init</span><span style="color: #f8f8f2">({</span>
        <span style="color: #a6e22e">centered</span><span style="color: #f92672">:</span><span style="color: #66d9ef">true</span>
    <span style="color: #f8f8f2">});</span>
<span style="color: #f92672">&lt;/script&gt;</span>
</pre></div>

        <p>
            Use the <code>centered</code> option when the content of your website is centered.
            This way, clicks will be recorded and displayed correctly for any screen resolution.
        </p>

        <h3>Install our chrome extension</h3>
        <p>
            To see an animated heatmap of your user's click behaviour, you need to install
            <a target="_blank" href="https://chrome.google.com/webstore/detail/infrared/micfbdajidndejakijomaiipgjbdjehj">our
            Chrome extension</a>. Once the extension is installed, go to the options for the extension
            and enter your API key: <em>{{ user.api_key }}</em>.
            Go to the page on your site for which you want to see a heatmap, and click the
            Infrared logo that's added to the right of your browser's address bar to start the animation.
        </p>

        <p>
            That's it! Have fun!
        </p>
        <div class="alert alert-success">
            Your API key: {{ user.api_key }}
        </div>

     </div>
 </div>
{% endblock %}