=== AI Entries ===

Contributors:      berchj  
Tested up to:      6.6  
Stable tag:        1.0.9  
License:           GPLv2 or later  
License URI:       https://www.gnu.org/licenses/gpl-2.0.html  
Tags:              AI, automated publishing, content generation, image-generation , news

== Short Description ==
A plugin that automates the creation of WordPress posts using Google AI, Stability AI, and News API for generating content and featured images.

== Description ==

This plugin uses Google artificial intelligence (GEMINI), Stability AI, and News API to automate the creation of WordPress posts based on configurable parameters from the WordPress admin view. It generates original content based on real articles and creates featured images accordingly.

To use this plugin, you must obtain API keys from all services:

- **GEMINI API**: You need a GEMINI API KEY, which you can get for free [here](https://ai.google.dev/gemini-api/docs/api-key).
  
    - **Service URL**: `https://ai.google.dev/`
    - **Terms of Use**: [Google API Terms](https://policies.google.com/terms)
    - **Privacy Policy**: [Google API Privacy](https://policies.google.com/privacy)

  
- **Stability AI API**: You also need a Stability.ai API KEY, available for free [here](https://platform.stability.ai/).

    - **Service URL**: `https://stability.ai/`
    - **Terms of Use**: [Stability AI Terms](https://stability.ai/terms-of-use)
    - **Privacy Policy**: [Stability AI Privacy](https://stability.ai/privacy-policy)

- **News API**: You need a News API KEY, which you can get for free [here](https://newsapi.org/).

    - **Service URL**: `https://newsapi.org`
    - **Privacy Policy**: [News API Privacy](https://newsapi.org/privacy)
    - **Terms of Use**: [News API Terms](https://newsapi.org/terms)

Please note that the performance and availability of functionalities depend on these APIs, and usage is subject to their respective terms and conditions.

== Installation ==

= Installation from within WordPress =

1. Visit **Plugins > Add New**.
2. Search for **AI Entries**.
3. Install and activate the plugin.

== Screenshots ==

1. Setup Wizard to generate posts

== Frequently Asked Questions ==

= Where can I contribute or report bugs of this plugin? =

This is an open-source project. All development for this plugin is handled via [GitHub](https://github.com/berchj/AIEntries). Any issues or pull requests should be posted there.

= Why aren't all posts always created? =

The functionality of this plugin depends on external systems accessed through HTTP calls. The performance of the plugin may be affected by the free API keys and the limitations or changes in the service provided by these APIs.

== Support ==

For support, email juliobermudezch@gmail.com .