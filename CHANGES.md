#### 1.5.8
* Fixed: Error "[] operator not supported for strings" in program adjacent entry pagination.

#### 1.5.7
* Fixed: Run My Account Programs list excerpt through do_shortcode.

#### 1.5.6.1
* Fixed: Undefined variable in early bail out of content hooks with Genesis.

#### 1.5.6
* Changed: Use Genesis hooks if G is active for wampum_before_content and wampum_after_content hooks.

#### 1.5.5
* Added: Support for Mai CPT settings in Customizer.
* Added: Internal github updater.

#### 1.5.4
* Change access_redirect() method to not redirect unless Woo Membership settings say so.
* Show the parent program restricted message on all child program pages.

#### 1.5.3
* Update plugin header to point to new repo in bizbudding GitHub organization

#### 1.5.2
* Add new filters to change post_type and taxo args

#### 1.5.1
* Add new wampum_prev_post_text and wampum_next_post_text filters
* Add genesis-layouts support for wampum_program CPT by default

#### 1.5.0
* New template system via functions
* Program templates taxonomy - 'wampum_program_template'
* Create wampum_do_template_{term_slug_with_underscores}() template tags for custom template based on term slugs
* Add helper function wampum_get_top_parent_id()
* Restrict access to all child, grandchild, etc pages of restricted programs
* Remove old resources functionality
* Remove widgets

#### 1.4.7.1
* Allow videos to embed in resource popup
* Add filter for resource popup width

#### 1.4.7
* Add wampum_create_user_membership() helper function to use a form to (maybe) create user, and add a user to a membership

#### 1.4.6.1
* Add helper action hook for wampum_popups to get safely loaded, including CSS

#### 1.4.6
* Resource list only shows 1 button for clarity
* Improve wampum_popup() function

#### 1.4.5
* Fix prev/next program step query not showing full results

#### 1.4.4
* Make resources a non-public post type
* Display Resources on single programs, styled like a popup, via a query var
* Programs now redirected to wc_memberships_redirect_page_id if user doesn't have access

#### 1.4.3
* Remove default padding from program progess widget

#### 1.4.2
* Add first step link to program parent entry pagination
* Program progress CSS-only checkmarks

#### 1.4.1
* Fix wampum_get_user_programs() function. Was showing all programs, even if no access

#### 1.4.0
* Breaking Changes!
* Convert wampum_step CPT to child pages of wampum_program CPT
* Convert Piklist everything to ACF Pro everything
* Must manually run helper functions in /includes/upgrade/functions-upgrade.php (See file comments)
* Manual upgrade coverts everything cleans up after itself

#### 1.3.0
* Add no access overlay, message, and buttons to programs and steps
* Rebuild rewrite rules to get closer to working correctly

#### 1.2.4
* Fix missing space between 'My Programs' on account page

#### 1.2.3
* Add version number when registering stylesheet

#### 1.2.2
* Update Gamajo Template Loader to 1.2.0
* Add parameter to allow wampum_get_template_part() helper function to pass $data into template
* Convert resource lists and program lists to template parts
* Change faux tables to new wampum specific markup for flexbox tables
* Bring back wampum.css stylesheet for base styles of tables and other future styling

#### 1.2.1
* Fix restricted programs/steps being visible if user is logged out

#### 1.2.0
* Add featured image support for resources
* Change resource table list to flexbox tables
* Fixed resource table header showing when program/step had no resources

#### 1.1.1
* Add download button to single resources

#### 1.1.0
* Auto-display connected resources on programs and steps

#### 1.0.2
* Update program steps widget to show optionally program name as widget title, and optionally link title to the program

#### 1.0.1
* Fix resource metabox not showing up

#### 1.0.01
* Remove custom account code

#### 1.0.0
* Official release on a live production site!!!!

#### 0.0.64
* Fix action links in account
* Fix accidental display of restricted programs in account and via single post

#### 0.0.63
* Set all wampum post types has_archive to false with a filter to change

#### 0.0.62
* Fix get_programs method in memberships class

#### 0.0.61
* Add logout link to my account nav

#### 0.0.60
* Cleanup template

#### 0.0.59
* Account programs bottom margin

#### 0.0.58
* Remove Flexgrid - should be in theme instead. New programs.php template

#### 0.0.57
* Update Flexgrid to v1.2.1, fix method error in orders.php template

#### 0.0.56
* Add excerpt support for wampum post types

#### 0.0.55
* Add isset() check when attempting to add data to $wp_query

#### 0.0.54
* Super refined prev/next connection links queries on all wampum singular pages

#### 0.0.53
* Fix for wampum queries running on home page (and every page?)

#### 0.0.52
* So much new, i'm terrible at changelogs
* Connection data in main $wp_query for more efficient calls to get steps and step program
* Yoast in breadcrumbs

#### 0.0.41
* Step progress now available per program
* Refined p2p connection code
* Fix random php errors
* Add logged in check to account dashboard

#### 0.0.4
* Restful p2p connections work!

#### 0.0.32
* Fix current-step class in program steps widget

#### 0.0.31
* Add new Program Steps widget, other minor fixes

#### 0.0.3
* create changelog file ;)
* reverse p2p connection to/from cause I had it mostly backwards
