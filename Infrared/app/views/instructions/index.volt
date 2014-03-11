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
            
            <!-- HTML generated using hilite.me --><div style="background: #272822; overflow:auto;width:auto;padding:.2em .6em;"><pre style="border:none;background: #272822;margin: 0; line-height: 125%">
<span style="color: #f92672">&lt;script </span><span style="color: #a6e22e">type=</span><span style="color: #e6db74">&quot;text/javascript&quot;</span> <span style="color: #a6e22e">id=</span><span style="color: #e6db74">&quot;{{ user.getPublicKey() }}&quot;</span><span style="color: #f92672">&gt;</span>
    <span style="color: #66d9ef">var</span> <span style="color: #a6e22e">infraredOptions</span> <span style="color: #f92672">=</span> <span style="color: #f8f8f2">{ </span><span style="color: #a6e22e">centered</span><span style="color: #f92672"> : </span><span style="color: #66d9ef">true</span><span style="color: #f8f8f2"> };</span>
    
    <span style="color: #f8f8f2">(</span><span style="color: #66d9ef">function</span><span style="color: #f8f8f2">(){</span>
        <span style="color: #66d9ef">var</span> <span style="color: #a6e22e">ir</span> <span style="color: #f92672">=</span> <span style="color: #f8f8f2">document.</span><span style="color: #a6e22e">createElement</span><span style="color: #f8f8f2">(</span><span style="color: #e6db74">&#39;script&#39;</span><span style="color: #f8f8f2">);</span>
        <span style="color: #a6e22e">ir</span><span style="color: #f8f8f2">.</span><span style="color: #a6e22e">src</span> <span style="color: #f92672">=</span> <span style="color: #f8f8f2">(</span><span style="color: #e6db74">&#39;https:&#39;</span> <span style="color: #f92672">==</span> <span style="color: #f8f8f2">document.</span><span style="color: #a6e22e">location</span><span style="color: #f8f8f2">.</span><span style="color: #a6e22e">protocol</span> <span style="color: #f92672">?</span> <span style="color: #e6db74">&#39;https://&#39;</span> <span style="color: #f92672">:</span> 
            <span style="color: #e6db74">&#39;http://&#39;</span><span style="color: #f8f8f2">)</span> <span style="color: #f92672">+</span> <span style="color: #e6db74">&#39;{{ host }}/plugin/infrared.js&#39;</span><span style="color: #f8f8f2">;</span>
        <span style="color: #a6e22e">ir</span><span style="color: #f8f8f2">.</span><span style="color: #a6e22e">setAttribute</span><span style="color: #f8f8f2">(</span><span style="color: #e6db74">&#39;async&#39;</span><span style="color: #f8f8f2">,</span> <span style="color: #e6db74">&#39;true&#39;</span><span style="color: #f8f8f2">);</span>
        <span style="color: #a6e22e">ir</span><span style="color: #f8f8f2">.</span><span style="color: #a6e22e">addEventListener</span><span style="color: #f8f8f2">(</span><span style="color: #e6db74">&#39;load&#39;</span><span style="color: #f8f8f2">,</span> <span style="color: #66d9ef">function</span> <span style="color: #f8f8f2">(</span><span style="color: #a6e22e">e</span><span style="color: #f8f8f2">)</span> <span style="color: #f8f8f2">{</span> <span style="color: #a6e22e">Infrared</span><span style="color: #f8f8f2">.</span><span style="color: #a6e22e">init</span><span style="color: #f8f8f2">(</span><span style="color: #a6e22e">infraredOptions</span><span style="color: #f8f8f2">);</span> <span style="color: #f8f8f2">},</span> <span style="color: #66d9ef">false</span><span style="color: #f8f8f2">);</span>
        <span style="color: #f8f8f2">document.</span><span style="color: #a6e22e">documentElement</span><span style="color: #f8f8f2">.</span><span style="color: #a6e22e">firstChild</span><span style="color: #f8f8f2">.</span><span style="color: #a6e22e">appendChild</span><span style="color: #f8f8f2">(</span><span style="color: #a6e22e">ir</span><span style="color: #f8f8f2">);</span>
    <span style="color: #f8f8f2">}());</span>
<span style="color: #f92672">&lt;/script&gt;</span>
</pre></div>

        <ul>
            <li>
                A variable <code>infraredOptions</code> is declared. Use the <code>centered</code>
                option when the content of your website is centered. This way, clicks will be
                recorded and displayed correctly for any screen resolution.
            </li>
            <li>
                The script tag has an id. When activating a domain in your Infrared account, we
                use this to verify you actually own the domain you're activating. (No one else would
                be able to place this unique token on the domain you're activating.)
            </li>
            <li>
                Our library will be loaded over https if your site is in https so we won't break
                your security.
            </li>
            <li>
                Our library is loaded asynchronously, so it won't block your page load. If, for 
                some reason, we can't serve you the library, at least your site will still load.
            </li>
        </ul>

        <h3>Install our chrome extension</h3>
        <p>
            To see an animated heatmap of your user's click behaviour, you need to install
            <a target="_blank" href="https://chrome.google.com/webstore/detail/infrared/micfbdajidndejakijomaiipgjbdjehj">our
            Chrome extension</a>. Once the extension is installed, go to the options for the extension
            and enter your API key and public key (see below).
            Go to the page on your site for which you want to see a heatmap, and click the
            play button that's added to the right of your browser's address bar to start the animation.
        </p>

        <h3>Your keys</h3>
        <p>
            <table>
                <tr>
                    <td>Your API key:</td>
                    <td><code>{{ user.api_key }}</code></td>
                </tr>
                <tr>
                    <td>Your public key:</td>
                    <td><code>{{ user.getPublicKey() }}</code></td>
                </tr>
            </table>
        </p>
        <p>
            That's it! Have fun!
        </p>
     </div>
 </div>
{% endblock %}