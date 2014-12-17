'use strict';

(function(_, $) {

    // ui module
    var ui = (function() {
        return {
            winWidth: function() {
                return $(window).width();
            },

            responsiveScroll: function() {
                $.ceEvent('on', 'ce.needScroll', function(opt) {

                    opt.need_scroll = false;
                    setTimeout(function() {
                        $.scrollToElm($('#' + opt.jelm.data('caScroll')));
                    }, 310);
                });
            },

            responsiveNotifications: function() {
                if(this.winWidth() <= 767) {
                    $.ceEvent('on', 'ce.notificationshow', function(notification) {
                        if($(notification).hasClass('cm-notification-content-extended')) {
                            $('body,html').scrollTop(0);
                        }
                    });
                }
            },

            responsiveTabs: function() {
                if(this.winWidth() <= 480) {

                    // conver tabs to accordion
                    $('.cm-j-tabs:not(.cm-j-tabs-disable-convertation)').each(function(index) {
                        var accordion = $('<div class="ty-accordion cm-accordion" id="accordion_id_' + index + '">');
                        var tabsContent = $('.cm-tabs-content');
                        var self = this;

                        // hide tabs
                        $(this).hide();
                        tabsContent.hide();

                        if(!$('#accordion_id_' + index).length) {

                            $(this).find('>ul>li').each(function() {
                                var id = $(this).attr('id');
                                var content = $('.cm-tabs-content #content_' + id).show();

                                // rename tab id
                                $(this).attr('id', 'hidden_tab_' + id);

                                accordion.append('<h3 id="' + id + '">' + $(this).text() + '</h3>');
                                $(content).appendTo(accordion);
                            });

                            $(self).before(accordion);
                        }
                    });

                    $('.cm-accordion').ceAccordion('reinit', {heightStyle : "content"});

                    var active = window.location.hash;
                    if(typeof active !== 'undefined' && $(active).length > 0) {
                        $(active).click();
                    }

                } else {
                    
                    $('.cm-accordion').accordion('destroy');
                    
                    $('.cm-accordion > div').each(function(index) {
                        $(this).hide();
                        $(this).appendTo($('.cm-tabs-content'));
                    });

                    $('.cm-accordion').remove();
                    
                    // remove prefix
                    $('.cm-j-tabs>ul>li').each(function(){
                        var id = $(this).attr('id').replace('hidden_tab_','');
                        $(this).attr('id', id);
                        
                        if($(this).hasClass('active')) {
                            $('#content_' + $(this).attr('id')).show();
                        }
                    });

                    $('.cm-j-tabs, .cm-tabs-content').show();
                }
            },

            cmToggleClass: function(e) {
                var jelm = $(e.target);

                // Toggle classes
                if (jelm.hasClass('cm-toggle-class') || jelm.parents().hasClass('cm-toggle-class')) {
                    var elm = jelm.hasClass('cm-toggle-class') ? jelm : jelm.parents('.cm-toggle-class:first');

                    var linked_elm = elm.attr('id').replace('tc_','');
                    var toggleClass = elm.data('caClass');

                    elm.toggleClass('active');
                    $('#' + linked_elm).toggleClass(toggleClass);

                }
            },

            responsiveMenu: function() {

                var menu_elm = $('.cm-responsive-menu');
                menu_elm.on('click', '.ty-menu__menu-btn', function() {

                    $(this).parent(menu_elm).find('.ty-menu__item').toggle();
                });

                menu_elm.on('click', '.cm-responsive-menu-toggle',function () {
                    $(this).toggleClass('ty-menu__item-toggle-active');
                    $('.icon-down-open', this).toggleClass('icon-up-open');
                    $(this).parent().find('.cm-responsive-menu-submenu').first().toggleClass('ty-menu__items-show');
                });
            },

            responsiveMenuLargeTouch: function() {

                var isTouch = (('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch);

                if (isTouch) {

                    var winWidth = ui.winWidth();

                    $(document).on('touchstart', function(e) {

                        if (ui.winWidth() >= 979) {

                            var elm = $(e.target);

                            if (elm.hasClass('cm-menu-item-responsive') || elm.closest('.cm-menu-item-responsive').length) {

                                var menuItem = elm.hasClass('cm-menu-item-responsive') ? elm : elm.closest('.cm-menu-item-responsive');

                                if (!menuItem.hasClass('is-hover-menu')) {
                                    e.preventDefault();
                                    $('.cm-menu-item-responsive').removeClass('is-hover-menu');
                                    menuItem.addClass('is-hover-menu');
                                }
                            }

                            var subMenu = $('.ty-menu__submenu-items');
                            if (subMenu.is(':visible') && !elm.closest('.cm-menu-item-responsive').length) {
                                $('.cm-menu-item-responsive').removeClass('is-hover-menu');
                            }

                        } else {
                            $('.cm-menu-item-responsive').removeClass('is-hover-menu');
                        }
                    });
                }
            },

            responsiveTables: function(e) {
                
                var tables = $('.ty-table');

                if(this.winWidth() <= 767) {
                    tables.each(function() {
                        var thTexts = [];

                        // if we have sub table detach it.
                        var subTable = $(this).find('.ty-table');
                        if(subTable.length) {
                            subTable.parent().attr('data-ca-has-sub-table', 'true');
                            subTable.parent().data('caSubTableData', subTable.detach());
                        }

                        $(this).find('th').each(function() {
                            thTexts.push($(this).text());
                        });

                        $(this).find('tr:not(.ty-table__no-items)').each(function() {
                            $(this).find('td:not(.ty-table-disable-convertation)').each(function(index) {
                                var $elm = $(this);
                                
                                if($elm.find('.ty-table__responsive-content').length == 0) {
                                    $elm.wrapInner('<div class="ty-table__responsive-content"></div>');
                                    $elm.prepend('<div class="ty-table__responsive-header">' + thTexts[index] + '</div>');
                                }
                            });

                        });

                        if(subTable.length) {
                            var $subTableElm = $(this).find('[data-ca-has-sub-table]');
                            $subTableElm.append($subTableElm.data('caSubTableData'));
                            
                            $subTableElm.removeAttr('data-ca-has-sub-table');
                            $subTableElm.removeData('caSubTableData');
                        }

                    });
                }
            },

            resizeDialog: function() {
                var self = this;
                var dlg = $('.ui-dialog');

                $('.ui-widget-overlay').css({
                    'min-height': $(window).height()
                });

                $(dlg).css({
                    'position':'absolute',
                    'width': $(window).width() - 20,
                    'left': '10px',
                    'top':'10px',
                    'max-height': 'none',
                    'height': 'auto',
                    'margin-bottom': '10px'
                });

                // calculate title width
                $(dlg).find('.ui-dialog-title').css({
                    'width': $(window).width() - 80
                });

                $(dlg).find('.ui-dialog-content').css({
                    'height': 'auto',
                    'max-height': 'none'
                });
                
                $(dlg).find('.object-container').css({
                    'height': 'auto'
                });

                $(dlg).find('.buttons-container').css({
                    'position':'relative',
                    'top': 'auto',
                    'left': '0px',
                    'right': '0px',
                    'bottom': '0px',
                    'width': 'auto'
                });

                $('body,html').scrollTop(0);

            },

            responsiveDialog: function() {
                var self = this;
                $.ceEvent('on', 'ce.dialogshow', function() {
                    if(self.winWidth() <= 767) {
                        self.resizeDialog();
                    }
                });
            }
        }

    })();

    $(document).ready(function() {

        $(window).resize(function(e){
            ui.winWidth();
            ui.responsiveTables();
            ui.responsiveMenuLargeTouch();
        });
        
        window.addEventListener('orientationchange', function() {
            if(ui.winWidth() <= 767) {
                ui.resizeDialog();
            }
            $.ceDialog('get_last').ceDialog('reload');
        }, false);

        ui.responsiveDialog();

        // notifications
        ui.responsiveNotifications();
        
        // init menu
        ui.responsiveMenu();
        ui.responsiveMenuLargeTouch();

        // responsive tables
        ui.responsiveTables();
        
        $.ceEvent('on', 'ce.ajaxdone', function() {
            ui.responsiveTables();

            if(ui.winWidth() <= 767) {
                ui.resizeDialog();
            }
        });

        // events handlers
        $(document).on('touchstart mousedown', function (e) {
            ui.cmToggleClass(e);
        });

        // event handler for window resize
        $.ceEvent('on', 'ce.tab.init', function() {
            $(window).resize(function(e){
                ui.responsiveTabs();
            });

            ui.responsiveTabs();
            ui.responsiveScroll();
        });

    });


}(Tygh, Tygh.$));
