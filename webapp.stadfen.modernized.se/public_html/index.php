<!DOCTYPE html>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Städfen Webapp</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

        <link rel="apple-touch-icon" href="apple-touch-icon.png">
        <!-- Place favicon.ico in the root directory -->
        <link rel="stylesheet" href="/stylesheets/styles.css">
        
        <script src="//use.typekit.net/ryn3vmd.js"></script>
<script>try{Typekit.load();}catch(e){}</script>
        
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
        
        <div class="popover-content-wrapper" id="popover_content_wrapper" style="display: none;">
              <form class="popover-newmessage-form">
              
              	<div class="form-control send-message-phonenr" onClick="$('#send-message-phonenr').focus();" style="cursor:text;">
                	<input class="send-message-input" id="send-message-phonenr" type="text" placeholder="Telefonnummer" autocomplete="off" style="outline:none;" onkeypress="return event.keyCode != 13;">
                </div>
                
                <div style="position: relative;">
                    <div class="form-control message-text-container" style="position: relative; height: 200px;">
                    <textarea id="send-message-text" class="send-message-text clearfix"></textarea>
                    </div>
                    <p class="text-length" style="position: absolute; bottom: 10px; right: 30px; margin: 0px;">0/0</p>
                </div>
                
                <a id="btn-send-message" class="btn btn-default btn-send-message" style="width: 120px;">Skicka</a>
                <a class="open-templates" style="float: right;">Bifoga mall</a>
              </form>
        </div>
        
        <div class="bg"></div>

        <!-- Add your site or application content here -->
        <div id="page_main" class="app-grid">
        	<div id="sidemenu" class="side-menu">
                <div class="menu-items">
                    <div id="sidemenubutton_conversations" class="item" onClick="NavigationSystem.Navigate('module_conversations convlist_active');updateMenuItem(this); ">
                    <img src="/images/icon-menu-inbox.svg" alt="inbox">
                    <div class="new-incoming-messages-box"></div>
                    </div>
                    <div id="sidemenubutton_statistics" class="item" onClick="NavigationSystem.Navigate('module_statistics');updateMenuItem(this); ">
                    <img src="/images/icon-menu-datatraffic.svg" alt="datatraffic">
                    </div>
                    <div id="sidemenubutton_settings" class="item" onClick="NavigationSystem.Navigate('module_settings account_overview');updateMenuItem(this); ">
                    <img src="/images/icon-menu-settings.svg" alt="settings">
                    </div>
                </div>
                
                <div class="bottom">
                	<div class="loading">
                            <img class="icon-loading" src="/images/icon-loading.svg" alt="loading">
                            <p class="message"></p>
                    </div>
                    <div class="input-group new-message">
                        <a id="button-newmessage" class="btn btn-sidemenu" data-container="body" data-toggle="popover" title="Nytt SMS" data-placement="top">Skicka SMS</a>
                    </div>
                </div>
            </div>

			<div id="main-content-holder">
            
            	<div class="module" id="module_conversations">
                    <div class="conversation-list">
                    
                        <div id="conversation-list-slider" class="tabing left">
                            <div class="list-padding">
                                <div class="menu">
                                    <a data-nav="module_conversations convlist_active" id="conversation_list_button_active">Inkorg (<span id="unreadcount_active"></span>)</a>
                                    <a data-nav="module_conversations convlist_inactive" id="conversation_list_button_inactive">Inaktiv inkorg (<span id="unreadcount_inactive"></span>)</a>
                                    <a data-nav="module_conversations convlist_archived" id="conversation_list_button_archived">Papperskorg</a>
                                </div>
                                <div class="header">
                                    <h1>Inbox</h1>
                                    <div id="conversation-list-search" class="input-group searchbar">
                                    <input type="text"  id="conv_search_textfield" class="form-control transparent" placeholder="Sök">
                                    <span class="input-group-btn">
                                        <button class="btn btn-image" type="button"><img src="/images/icon-input-search.png" alt="search"></button>
                                    </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="conversation_list" class="active-container">
                            
                            <div class="convlist_list" id="convlist_active"></div>
                            <div class="convlist_list" id="convlist_inactive"></div>
                            <div class="convlist_list" id="convlist_archived"></div>
                            
                                <?php /*?><div class="conversation-item">
                                    <div class="right-bar">
                                        <img src="/images/icon-conversation-starred.svg">
                                    </div>
                                    <div class="number">
                                        <h2>+46707838026</h2>
                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididuntutlabore et</p>
                                    </div>
                                </div><?php */?>
                                
                            </div>
                        </div>
                    
                    </div>
                    
                    <div id="current_conversation" class="conversation-thread">
                        <div class="header clearfix">
                                <h1 id="convname_disp"></h1>
                                <img class="reply-button" src="/images/icon-reply.svg" alt="reply">
                        </div>
                        <div class="messages" id="messages_scroll">
                            <div class="load-more-conversation">
                            <button id="btn_furthermsgs" onClick="Conversations.LoadFurtherMessages();" class="btn btn-default">Ladda ytterligare meddelanden</button>
                            </div>
                            <div id="current_conversation_main"></div>
                            
                        </div>
                        <div class="reply-container">
                            <div class="inner">
                                <div class="form-control message-text-container" style="position: relative; height: 100%;">
                                <textarea id="reply-message-text" class="send-message-text clearfix" placeholder="Svara konversation..."></textarea>
                                </div>
                                <div class="action-bar">
                                <img class="attach-template" src="/images/icon-attach-template.svg" alt="attach-template"><a role="button" class="btn btn-default" id="reply-btn-send">Skicka</a>
                                </div>
                                                            <p class="reply-character-count hidden" style="position: absolute; bottom: 0px; right: 20px;">0/0</p>
                            </div> 
                        </div>
                    </div> 
                </div> <!-- CONVERSATION MODULE -->
                
                <!-- STATISTICS MODULE -->
                <div class="module" id="module_statistics">

                        <div class="statistics-container">
                        
                        <div id="statistics_currentuser"></div>
                        
                        <div class="statistics-time-display"><a href="javascript:StatisticsPage.NavigatePrevMonth();">&lt;&lt;</a><span id="statistics_current_month"></span><a href="javascript:StatisticsPage.NavigateNextMonth();">&gt;&gt;</a></div>
                        
                        <div class="statistics-chart-container">
                            <canvas id="bar-chart-statistics" width="1100" height="90"></canvas>
                        </div>
                        
                        <br>
                        <div class="statistics-account-list">
                            <table id="statistics_accountlist_table" class="settings-table">
                                <thead>
                                    <tr>
                                        <th>Visningsnamn</th>
                                        <th>E-postadress</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            <br>
                            <button id="statistics_btn_allaccounts" class="btn btn-default" onclick="StatisticsPage.NavigateAccount(0);return false;">Visa för alla konton</button>
                        </div>
                        </div>
            	</div> <!-- STATISTICS MODULE -->
            
                <div class="module" id="module_settings">
                    <div class="settings_content">
                    	
                        <div><h1>Kontoinställningar</h1></div>
                        
                    	<div id="settings-tabs-container" class="tabing left">
                        
                        <div class="menu" style="padding: 10px 0px; border-bottom: 1px solid #CDCDCD; border-radius: 4px; margin-top: 20px; margin-bottom: 30px;">
                        	<a data-nav="module_settings account_overview settingspage" id="settings_overview">Konto</a>
                            <a data-nav="module_settings account_templates settingspage" id="settings_templates">Mallar</a>
                            <a data-nav="module_settings account_signatures settingspage" id="settings_signature">Signatur</a>
                            <a data-nav="module_settings account_silentmode settingspage" id="settings_silentmode">Tyst läge</a>
                            <a data-nav="module_settings account_accountlist settingspage" id="settings_accountlist">Kontolista</a>
                        </div>
                        <img class="loading-icon" src="/images/icon-loading.svg" alt="loading">
                        
                        
                        
                        <div class="active-container">
                        
                        <div id="account_overview">
                            <div class="form-group">
                            	<h2>Mitt konto</h2>
                                <button type="button" class="btn btn-default" id="btnlogout_1" onclick="Logout();">Logga ut</button>
                                <button type="button" class="btn btn-default" id="btnchangepass_1" onclick="showNewPWPane();">Ändra lösenord</button>
                                <form id="changepw_pane" style="width: 350px; margin: 20px 0px;">
                                        <label style=" font-weight: 300;">Nytt lösenord: <input class="form-control" type="password" id="newpassword"/></label><br>
                                        <label style=" font-weight: 300;">Upprepa: <input class="form-control" type="password" id="repeatpassword"/></label><br>
                                        <button type="button" class="btn btn-default" onclick="SettingsFunc.ClickSaveNewPass(); return false;"  style="margin-top: 20px;">Spara nytt lösenord</button>
                                </form>
                                <form>
                                    <div>
                                        <p style="margin-top: 25px;">Visningsnamn</p>
                                        <span id="mydn_display_pane">
    
                                            <input class="form-control" type="text" id="mydn_edit" style="width: 300px;"/>
                                            <button type="button" class="btn btn-default" onclick="SettingsFunc.SaveDisplayNameSign();return false;" id="btn_savemydn">Spara</button>
                                        </span>
                                        
                                    </div>
                            	</form>
                            </div>

<?php /*?>                            <div class="form-group">
                                <h3>Mina behörigheter</h3>
                                <div class="priv">
                                    <div id="mypriv_editaccount">
                                        <p>Hantering av konton</p>
                                        <h1></h1>
                                    </div>
                                    <div id="mypriv_allconvs">
                                        <p>Tillgång till konversationer</p>
                                        <h1></h1>
                                    </div>
                                    <div id="mypriv_companytemplate">
                                        <p>Redigera företagsmallar</p>
                                        <h1></h1>
                                    </div>
                                    <div id="mypriv_companysignature">
                                        <p>Redigera företagssignatur</p>
                                        <h1></h1>
                                    </div>
                                    <div id="mypriv_companytraffic">
                                        <p>Tillgång till företagstrafik</p>
                                        <h1></h1>
                                    </div>
                                </div>
                            </div><?php */?>
                        </div>
                        
                        <div id="account_templates">
                            <div class="form-group">
                                <h2>Mallar</h2>
                                <div>
                                    <h3>Mina mallar</h3>
                                    <p>Mina personliga mallar.</p>
                                    <table id="mytemplate_table" class="settings-table">
                                        <thead>
                                            <tr>
                                            	<th></th>
                                                <th>Namn</th>
                                                <th>Innehåll</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                    <div id="mytemplate_none">Du har inga mallar tillagda</div>
                                </div>
                                
                                <button type="button" class="btn btn-default" id="btn_createat" onClick="SettingsFunc.ShowTemplatePane(TEMPLATE_TYPE_ACCOUNT); return false;">Skapa ny mall</button>
                            </div>
                            <div class="form-group">
                                <form class="addtemplate_pane" id="newatemplate">
                                    <h4>Ny mall</h4>
                                    <label>Namn: </label><input class="form-control" type="text" id="tname_a">
                                    <label>Text: </label><textarea class="form-control" id="tcontent_a"></textarea>
                                    <button type="button" class="btn btn-default" onClick="SettingsFunc.HideTemplatePane();return false;">Avbryt</button> <button type="button" class="btn btn-default" onClick="SettingsFunc.ClickSaveTemplate(TEMPLATE_TYPE_ACCOUNT); return false;">Spara</button>
                                </form>
                            </div>
                            <div id="companytemplate_settings">
                                <div class="form-group">
                                <h3>Företagsmallar</h3>
                                <p>Företagets mallar.</p>
                                <table id="companytemplate_table" class="settings-table">
                                    <thead>
                                        <tr>
                                        	<th></th>
                                            <th>Namn</th>
                                            <th>Innehåll</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                                <div id="companytemplate_none">Det finns inga företagsmallar tillagda</div>
                                <button type="button" class="btn btn-default" id="btn_createct" onClick="SettingsFunc.ShowTemplatePane(TEMPLATE_TYPE_CUSTOMER); return false;">Skapa ny företagsmall</button>
                                <form class="addtemplate_pane" id="newctemplate">
                                    <h4>Ny företagsmall</h4>
                                    <label>Namn: </label><input class="form-control" type="text" id="tname_c">
                                    <label>Text: </label><textarea class="form-control" id="tcontent_c"></textarea>
                                    <button type="button" class="btn btn-default" onClick="SettingsFunc.HideTemplatePane();return false;">Avbryt</button> <button type="button" class="btn btn-default" onClick="SettingsFunc.ClickSaveTemplate(TEMPLATE_TYPE_CUSTOMER); return false;">Spara</button>
                                </form>
                                </div>
                            </div>
                        </div>
                        <div id="account_signatures">
                        	<h2>Signatur</h2>
                            <div class="form-group">
                                <form class="signature-form">
                                    <h3>Min signatur</h3>
                                    <div>
                                        <span id="mysignature_display_pane">
                                            <textarea class="form-control" id="mysignature_edit" rows="4">Ingen signatur definerad</textarea>
                                             <button type="button" class="btn btn-default" onclick="SettingsFunc.SaveMySign();return false;" id="btn_savemysign">Spara</button>
                                             <span id="companysignature_notification" style="display:none;">Företaget har valt att definiera signaturer centralt, din signatur kommer därför inte visas</span>
                                        </span>
                                        
                                    </div>
                                </form>
                            </div>
                            
                            <div id="companysignature_settings">
                                <div class="form-group">
                                    <form>
                                        <h3>Företagssignatur</h3>
                                        <div style="display: flex; justify-content: space-between;">
                                            <p>Aktivera företagssignatur</p>
                                            <div class="checkbox-default">  
                                              <input type="checkbox" id="compsign_enabled" name="compsign_enabled" onChange="SettingsFunc.ClickCompSignCheckbox();" />
                                              <label for="compsign_enabled"></label>
                                            </div>
                                        </div>
                                        <div style="margin-top: 20px;">
                                            <span id="compsignature_display_pane">
                                                <textarea class="form-control" id="compsignature_edit" rows="4">Ingen signatur definerad</textarea>
                                                <button type="button" class="btn btn-default" onclick="SettingsFunc.SaveCompSign();return false;" id="btn_savecompsign">Spara</button>
                                            </span>            
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div id="account_silentmode">
                            <div class="form-group">
                            <h2>Tyst läge</h2>
                                <div style="margin-top: 20px;">

                                    <p>Du kan aktiverat tyst läge för att slippa höra inkommande meddelanden utanför arbetstid i din telefon. </p>
                                    <p id="settings_manualsilent_status"></p>
                                    <button type="button" class="btn btn-default" id="btn_manual_silentmode_activate" onClick="SettingsFunc.ActivateManualSilentMode();">Aktivera</button>
                                    <button type="button" class="btn btn-default" id="btn_manual_silentmode_deactivate" onClick="SettingsFunc.DeactivateManualSilentMode();">Avaktivera</button>
                                    
                                    
                                     <table id="intervallist_table" class="settings-table">
                                        <thead>
                                            <tr>
                                                <th>Börjar dag</th>
                                                <th>Tid</th>
                                                <th>Slutar dag</th>
                                                <th>Tid</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                    <button type="button" class="btn btn-default btn_silentmode_save" onClick="SettingsFunc.NewSilentInterval();">Lägg till intervall</button>
                                    <button type="button" class="btn btn-default btn_silentmode_save" onClick="SettingsFunc.SaveIntervals();">Spara</button>
                                    <div style="width: 100%; position: relative; cursor: pointer;">
                                    	<canvas id="silent-mode-timeline"></canvas>
                                        
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        
                        <div id="account_accountlist">
                            <div id="accountlist_settings">
                                <div class="form-group">
                                	<div style="margin-bottom: 30px;">
                                    <div class="account-header-container"><p style="text-transform: uppercase; font-size: 0.9em; margin: 6px 0px; color:#FFF;">Plan</p><p style="font-weight: 500; color:#FFF;">Du har skapat <span id="settings_acc_c"></span> av <span id="settings_acc_m"></span> konton.<span id="setting_acc_nomore">Du kan därför inte skapa fler konton</span></p><button type="button" class="btn btn-secondary" onClick="SettingsFunc.StartCreateAccount();return false;" style="margin: 10px 0px; width: 200px; padding: 8px inherit;">Skapa konto</button></div>
                                        <h2>Kontolista</h2>
                                        <p>Alla aktiva konton i företaget.</p>
                                        
                                         <div id="buttonbar_createaccount">
                                        
                                    </div>
                                    </div>
                                    <div class="input-group searchbar">
                                        <input type="text"  id="accounts_search_textfield" class="form-control transparent" placeholder="Sök" onChange="EAFunc.OnSearchEdit();" onKeyDown="EAFunc.OnSearchEdit();">
                                        <span class="input-group-btn">
                                            <button class="btn btn-image" type="button"><img src="/images/icon-input-search.png" alt="search"></button>
                                        </span>
                                    </div>
                                    <table id="accountlist_table" class="settings-table">
                                        <thead>
                                            <tr>
                                                <th>Visningsnamn</th>
                                                <th>E-postadress</th>
                                                <th>Huvudkonto</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group" id="edittemplateform" style="display:none;">
                    	<h2>Redigera mall</h2>
                        
                        <form>
                        	<input class="form-control" type="text" id="tname_e"/><br>
                            <textarea class="form-control" id="tcontent_e"></textarea><br>
                            
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default" onclick="SettingsFunc.EndEditTemplate();return false;">Avbryt</button>
                                <button type="button" class="btn btn-default" onclick="SettingsFunc.SaveTemplateEdit();return false;">Spara</button>
                            </span>
                        </form>
                    </div>
                    
                    </div>
                    
                    </div>
                    
                </div> <!-- SETTINGS MODULE -->
                
                <div class="module" id="module_editaccount">
                
                <div class="settings_content">
                    <div class="form-group">
                    
                    <div id="heading_newacc"><h2>Skapa Konto</h2></div>
                    <div id="heading_editacc"><h2>Redigera Konto</h2></div>
                    
                    <div id="editacc_createsucces"><h2>Kontot skapat!</h2></div>

                    <div><p>E-postadress: <strong id="ea_email"></strong></p><form><input class="form-control" type="email" id="eac_email" style="width: 400px;"/></form></div>
                    <div id="displayname_part"><p>Visningsnamn: <strong id="ea_displayname"></strong></p></div>
                    
                    <div id="editacc_buttonbar1">
                    	<button type="button" class="btn btn-default" onclick="EAFunc.ButtonCancelAccount();return false;" id="ea_cancelaccount">Avsluta konto</button>
                    	<button type="button" class="btn btn-default" onclick="EAFunc.ButtonGeneratePassword();return false;">Generera nytt lösenord</button>
                        <span id="ea_password_generated">Nytt lösenord skickat till användare!</span></div>
                    
                    </div>
                    
                    <div class="form-group">
                    <h3>Behörigheter</h3>
                    <p>Välj vilken behörighet du vill att kontot ska ha.</p>
                    <table class="settings-table transparent">
                    	<thead>
                        	<tr>
                                <th>Inställning</th>
                                <th class="right">Behörighet</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>Kontoadministratör</td><td class="right">
                            <div class="checkbox-default"><input type="checkbox" id="eapriv_editaccount" name="eapriv_editaccount" class="editpriv_checkbox"/><label for="eapriv_editaccount" class="editpriv_checkbox_label"></label></div>
                            </td></tr>
                            <tr><td>Konversationsadministratör</td><td class="right"><div class="checkbox-default"><input type="checkbox" id="eapriv_allconvs" name="eapriv_allconvs" class="editpriv_checkbox"/><label for="eapriv_allconvs" class="editpriv_checkbox_label"></label></div></td></tr>
                            <tr><td>Redigera företagsmallar</td><td class="right"><div class="checkbox-default"><input type="checkbox" id="eapriv_companytemplate" name="eapriv_companytemplate" class="editpriv_checkbox" /><label for="eapriv_companytemplate" class="editpriv_checkbox_label"></label></div></td></tr>
                            <tr><td>Redigera företagssignatur</td><td class="right"><div class="checkbox-default"><input type="checkbox" id="eapriv_companysignature" name="eapriv_companysignature" class="editpriv_checkbox" /><label for="eapriv_companysignature" class="editpriv_checkbox_label"></label></div></td></tr>
                            <tr><td>Tillgång till företagstrafik</td><td class="right"><div class="checkbox-default"><input type="checkbox" id="eapriv_companytraffic" name="eapriv_companytraffic" class="editpriv_checkbox" /><label for="eapriv_companytraffic" class="editpriv_checkbox_label"></label></div></td></tr>
                        </tbody>
                    </table>
                    
                    <div id="buttonbar_savepriv"><button type="button" class="btn btn-default" onclick="EAFunc.SavePrivEdit();return false;" id="savepriv_button">Spara behörigheter</button></div>
                    </div>
                    
                    <div class="form-group">
                        <div id="ea_section_traffic">
                        </div>
                    </div>
                    
                    <div class="form-group">
                    <h3>Behörighet till konversationer</h3>
                    <p>Vilka konversationer som kontot ska ha tillgång till.</p>
                    <table class="settings-table transparent" id="editacc_convlist_list">
                        <thead>
                        <tr>
                            <th>Nummer</th>
                            <th>Namn</th>
                            <th class="right">Behörighet</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    </div>
                    
                    <div id="buttonbar_saveconvpriv"><button type="button" class="btn btn-default" onclick="EAFunc.SaveConvPrivEdit();return false;" id="saveconvpriv_button">Spara Konversationsbehörigheter</button></div>
                    
                    <div id="buttonbar_savenewaccount"><button type="button" class="btn btn-default" onclick="EAFunc.ButtonSaveNewAccount();return false;">Spara nytt konto</button></div>
                </div>
            </div>  <!-- EDIT ACCOUNT MODULE -->
                
            </div>
            
        </div>
        
        <div id="page_loadingscreen">
            <div class="absolute-center" style="width: 150px; height: 150px;"> 
            <canvas id="bubble-canvas-loading" class="bubble-canvas" width="150" height="150"></canvas>
                <p class="loading-screen-message">Ansluter till <span id="loadingscreen_number"></span>.</p>
            </div>
        </div>
        
        <div id="page_login">
    	<div style="width: 100vw; height: 100vh;">
            <div class="absolute-center" style="width: 360px; height: 465px;">
            	<img src="/images/Logo.png" style="width: 35%; margin: 0 auto; display: block; padding-bottom: 25px;" alt="logo">
                <div class="login-popup">
                    <div style="display: block; margin-bottom: 20px; margin-top: 30px; text-align: center;"></div>
                    <form id="loginform">
                    <div class="form-group"> 
                        <label for="loginname">Användarnamn</label>
                        <input type="text" placeholder="" class="form-control" id="loginname" style="border: 0px !important;">
                    </div>
                    <div class="form-group">
                        <label for="loginpassword">Lösenord</label>
                        <input type="password" placeholder="" class="form-control" id="loginpassword" style="border: 0px !important;">
                    </div>
                    <div class="form-group">
                        <button class="btn btn-default" role="button" style="width: 100%; margin-top: 25px; padding-top: 15px; padding-bottom: 15px; font-size: 1em; text-transform: uppercase;" onClick="loginSubmit();return false;" id="loginbutton">Logga in</button>
                    </div>
                    </form>
                    
                    <div id="message">&nbsp;</div>
                </div>
                <p style="text-align: center;">Städfen applikation. Version 1.0.0.</p>
            </div>
        </div>
    </div>
    
    <div id="page_init" style="width:100%; height:100%; position:absolute; left:0px; top:0px; background-color:#FFF;">
    	<canvas id="bubble-canvas-init" class="bubble-canvas" width="150" height="150" style="position:absolute; display:block; left:50%; top: 50%; margin-left:-75px; margin-top:-75px;"></canvas>
    </div>

		<!-- JQUERY -->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="/javascripts/vendor/jquery-1.11.2.min.js"><\/script>')</script>
        <!-- JQUERY MOBILE -->
        <!-- Include jQuery Mobile stylesheets -->
		<!-- Include the jQuery Mobile library -->
		<script src="/javascripts/vendor/jquery.mobile.custom.min.js"></script>
        
        <!-- OTHER VENDOR -->
        <script src="/javascripts/vendor/jquery.flexText.min.js"></script>

        <!-- BOOTSTRAP -->
        <script src="/javascripts/bootstrap.min.js"></script>
        
        <script src="/javascripts/plugins.js"></script>
        <script src="/javascripts/main.js"></script>
        
        <!-- CUSTOM MODERNIZED PLUGINS DEVELOPMENT VERSION -->
        <script src="/javascripts/custom/mdrz-slider.js"></script>
        <script src="/javascripts/custom/mdrz-autocomplete.js"></script>
        <script src="/javascripts/custom/mdrz-messagebox.js"></script>
        <script src="/javascripts/custom/mdrz-menu.js"></script>
        <script src="/javascripts/custom/mdrz-timeline.js"></script>
        <script src="/javascripts/custom/mdrz-setorder.js"></script>
        
        <!-- WEB SYSTEM -->
        <script src="/javascripts/webapp/definitions.js"></script>
        <script src="/javascripts/webapp/mobile.js"></script>
		<script src="/javascripts/webapp/ajaxengine.js"></script>
        <script src="/javascripts/webapp/comsystem.js"></script>
        <script src="/javascripts/webapp/webapp-system.js"></script>
        <script src="/javascripts/webapp/stuff.js"></script>
        <script src="/javascripts/webapp/settingspage.js"></script>
        <script src="/javascripts/webapp/conversations.js"></script>
        <script src="/javascripts/webapp/statistics.js"></script>
        <script src="/javascripts/webapp/sounds.js"></script>
        <script src="/javascripts/webapp/signature.js"></script>
        <script src="/javascripts/webapp/systemsettings.js"></script>
        <script src="/javascripts/webapp/loader.js"></script>
        <script src="/javascripts/webapp/templates.js"></script>
        <script src="/javascripts/webapp/loadingcanvas.js"></script>
        <script src="/javascripts/webapp/ui.js"></script>
        <script src="/javascripts/webapp/notifications.js"></script>
        
        <script src="/javascripts/Chart.min.js"></script>
        
		    <script>
	
	window.requestAnimFrame = (function(callback) {
return window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || window.oRequestAnimationFrame || window.msRequestAnimationFrame ||
function(callback) {
  window.setTimeout(callback, 1000 / 60);
};
})();
	
	var startTime = new Date();
	
	//Keeps track of the server time, used for loading
	var SessionTime =
	{
		loginServerTime: 0,
		loginClientTime : null,
		
		getElapsedSeconds : function()
		{
			var current = new Date();
			var seconds = (current.getTime() - SessionTime.loginClientTime.getTime()) / 1000;
			return seconds;
		},
		
		getServerTime : function()
		{
			return parseInt(SessionTime.loginServerTime + SessionTime.getElapsedSeconds());
		},
		
		setNow : function(serverTime)
		{
			SessionTime.loginServerTime = parseInt(serverTime);
			SessionTime.loginClientTime = new Date();
		},
		
		hasTime : function()
		{
			return SessionTime.loginClientTime != null;
		}
	}
	
	function loginSubmit()
	{
		var loginName = $("#loginname").val();
		var loginPass = $("#loginpassword").val();
		
		var $loginform = $("#loginform");
		
		ComSystem.Request("login",{user: loginName, pass: loginPass, systype: SESSIONTYPE_WEB},function(data,data2)
		{
			if(data.LoginSuccess)
			{
				// Navigate to the loading screen
				document.getElementById('loadingscreen_number').innerHTML = Phonenumber.GetDisplayStyle(data.Number);
				NavigationSystem.Navigate('loading_screen');
				
				setCookie("loggedin","true");
				NavigationSystem.LoggedIn();
				OnLoggedIn();
				
				var canvas = document.getElementById('bubble-canvas-loading');
				CircleLoadingAnimation.Create(canvas);
				CircleLoadingAnimation.BeginAnimation();
				
				setTimeout(function() {
					NavigationSystem.Navigate(STARTING_PAGE);
					CircleLoadingAnimation.Destroy();
				}, 2000);
			}
			else
			{
				alert("Du lyckades inte logga in. Var vänligen försök igen.");
			}
		}, document.getElementById("loginbutton"));
	}
	
	var OnLoggedIn = function()
	{
		Conversations.LoadLists();
		LoadSystemSettings();
		initPage();
	};
	
	ComSystem.Events.OnNotLoggedIn = function()
	{
		setCookie("loggedin","false");
		location.reload();
	};
	
	function Logout()
	{
		ComSystem.Request(ComSystem.Items.LOGOUT,null,function(data)
		{
			if(data.Success)
			{
				showInitScreen();
				NavigationSystem.LoggedOut();
				setCookie("loggedin","false");
				onLoggedOut();
			}
			else
			{
				popupbox.openPopup(500, "Fel", "<p class='popup-p'>Det gick inte att logga ut. Vänligen försök igen.</p>", new OkDialog());
			}
		});
	}
	
	function onLoggedOut()
	{
		clearApp();
		location.reload();
	}
	
	$(document).ready(function(e) {
		
		$.ajaxSetup(
		{
			crossDomain: true,
    		xhrFields: 
			{
        		withCredentials: true
    		},
			
			});
		
		if(getCookie("loggedin") == "true")
		{
			NavigationSystem.LoggedIn();
			OnLoggedIn();
			
			//Navigate
			if(window.location.pathname.indexOf("/user/") === 0)
			{
				var classSet = decodeURIComponent(window.location.pathname.replace("/user/",""));
				NavigationSystem.InternalNavigate(classSet);
			}
		}
        else
		{
			hideInitScreen();
		}
		
		resize_stuff();
    });
	
	function update()
	{
		if(NavigationSystem.isLoggedIn)
			LOAD(true,Conversations.Current,null);
		
		if(NavigationSystem.isLoggedIn)
			setTimeout(update,SystemSettings.WEBAPP_UPDATE_PERIOD);
	}
	
	$(window).resize(function(e) {
		MobileSystem.Execute();
        resize_stuff();
    });
	
	
	var resize_stuff = function()
	{
		$("body").css("overflow","hidden");
		var topbar_height = 73;
		var window_width = $(window).width();
		var window_height = $(window).height();
		var load_more_convs_height = $(".load-more-conversation").outerHeight()
		$("#convlist_list").css("height",(window_height - 180) + "px");
		//var reply_container_height = $(".reply-container").outerHeight()
		$("#current_conversation_main").height(window_height - topbar_height - load_more_convs_height - 1).css("top",topbar_height + "px");
		var sw = $("#sidemenu").outerWidth();
		var $cls =  $("#conversation-list-slider")
		var clw = $cls.is(":visible") ? $cls.outerWidth() : 0;
		$("#current_conversation .messages").width(Math.min(window_width -sw  -clw, 2048) - 1 );
	}

	var $convmain;
	var sort_base_val = 1000000;

	function onSelectConversation(ConvID)
	{
		if(ConvID == Conversations.Current)
			return;
		$('.conversation-item').removeClass('selected');
		$('#convlistitem_'+ConvID).addClass('selected');
		
		var $convItem = $(Conversations.GetConvListItem(ConvID));
		
		
		//Figure out category
		var category;
		
		if($convItem.data("archived") === true)
		{
			category = 'convlist_archived';
		}
		else if($convItem.data("active") === true)
		{
			category = 'convlist_active';
		}
		else
		{
			category = 'convlist_inactive';
		}
		
		NavigationSystem.Navigate("module_conversations " + category + " conv conv" + ConvID);
		
		//$(Conversations.GetConvListItem(ConvID)).addClass("selectedConv");
	}
	
	// Använd vid inladdning av data eller annan typ av krävande information
	var ApplicationLoading =
	{
		IsLoading : false,
		LoadingImage : '/images/icon-loading.svg',
		Duration : 5000,
		Stack : new Array(),
		
		Show : function(message) {
			$('#sidemenu .bottom .loading img').addClass('animate');
			$('#sidemenu .bottom .loading').show();
			$('#sidemenu .bottom .loading .message').html(message);
			ApplicationLoading.IsLoading = true;
		},
		Done : function() {
			
			if (ApplicationLoading.Stack.length > 0)
			$('#sidemenu .bottom .loading img').removeClass('animate');
			
			ApplicationLoading.IsLoading = false;
			ApplicationLoading.CallNotificationStack();	
		},
		CallNotificationStack : function() {
			if (ApplicationLoading.Stack.length > 0)
			{
				var current_not = ApplicationLoading.Stack[ApplicationLoading.Stack.length-1];
				
				$('#sidemenu .bottom .loading .icon-loading').attr('src', current_not.image);
				$('#sidemenu .bottom .loading .message').html(current_not.message);
				$('#sidemenu .bottom .loading').show().delay(ApplicationLoading.Duration).fadeOut(200, function() {
					
					current_not = null;
					ApplicationLoading.Stack.pop();
					
					ApplicationLoading.CallNotificationStack();
					
				});
			}
			else
			{
				ApplicationLoading.Reset();
				return;	
			}
		},
		Reset : function()
		{
			ApplicationLoading.Hide(function() {
				
				$('#sidemenu .bottom .loading .icon-loading').attr('src', ApplicationLoading.LoadingImage);
				$('#sidemenu .bottom .loading .message').html('');
				$('#sidemenu .bottom .loading img').removeClass('animate');
			
			});

		},
		Notify : function(message, image)
		{
			ApplicationLoading.Stack.push({ message: message, image: image });
		},
		Success : function(message)
		{
			ApplicationLoading.Notify(message, '/images/icon-success.svg');
		},
		Hide : function(callback) {
			// Set null as default
			callback = typeof callback !== 'undefined' ? callback : null;
			$('#sidemenu .bottom .loading').fadeOut(200, callback);
			ApplicationLoading.IsLoading = false;
		},
		CurrentState : function() {
			return ApplicationLoading.IsLoading;
		},
		Error : function(message) {
			ApplicationLoading.Notify('Felkod: ' + message, '/images/icon-alert.svg');
		}
		
	};
	
	
	
	
	
	
	
	
	
	NavigationSystem.AfterNavigate = function(strClasses)
	{
		$("div.active-container").css("display","");
		$("#edittemplateform").css("display","none");
		//Hide stuff that should hide on navigation
		$("#changepw_pane").hide();
		
		updateMenuItem();

		var $body = $("body");
		var is_conv = false;
		if(!$body.hasClass("conv"))
		{
			Conversations.InternalCloseCurrent();
		}
		if(!$body.hasClass("module_settings"))
			settinsgpage_loaded = false;
		if($body.hasClass("conv"))
		{
			var classes = strClasses.split(" ");
			for(var i = 0; i < classes.length; i++)
			{
				if(classes[i].match(/^conv[0-9]/))
				{
					//Conv id class
					//Extract conv id
					var ConvID = classes[i].replace("conv","");
					
					if(isnum(ConvID))
					{
						
						//Get conversation info from local list object¨
						var listObj = Conversations.GetConvListItem(ConvID);
						var $listObj = $(listObj);
						var convData = $listObj.data("convData");
						
						is_conv = true;
						if(SystemSettingsLoaded)
							LoadupConversation(ConvID);
						else
							LoadupConversation_WaitForSystemSettings(ConvID);
						break;
					}
				}
			}

		}
		else if($body.hasClass("module_statistics"))
		{
			StatisticsPage.AccountID = 0;
			
			var sDate = new Date();
			
			StatisticsPage.Month = sDate.getMonth() + 1;
			StatisticsPage.Year = sDate.getFullYear();
			
			//Set vars depending on url
			var classes = strClasses.split(" ");
			for(var i = 0; i < classes.length; i++)
			{
				if(classes[i].match(/^m[0-9]/))
				{
					var _month = classes[i].replace("m","");
					
					if(isnum(_month) && parseInt(_month) > 0)
					{
						StatisticsPage.Month =  parseInt(_month);
					}
				}
				else if(classes[i].match(/^y[0-9]/))
				{
					var _year = classes[i].replace("y","");
					
					if(isnum(_year) && parseInt(_year) > 0)
					{
						StatisticsPage.Year = parseInt(_year);
					}
				}
				else if($body.hasClass("account") && classes[i].match(/^account[0-9]/))
				{
					var _accID = classes[i].replace("account","");
					
					if(isnum(_accID) && parseInt(_accID) > 0)
					{
						StatisticsPage.AccountID =  parseInt(_accID);
					}
				}
			}
			
			StatisticsPage.Load();
		}
		else if ($body.hasClass("module_conversations"))
		{
			if ($body.hasClass("convlist_active"))
			{
				conversationSlider.setActiveMenu(0);
			}
			else if ($body.hasClass("convlist_inactive"))
			{
				conversationSlider.setActiveMenu(1);
			}
			else if ($body.hasClass("convlist_archived"))
			{
				conversationSlider.setActiveMenu(2);
			}
		}
		else if($body.hasClass("module_settings"))
		{

			if(!settinsgpage_loaded)
			{
				$('#settings-tabs-container').addClass('loading');
				settinsgpage_loaded = true;
			}
				
			onSettingsEnter();
			
			if ($body.hasClass("account_overview"))
			{
				settingspage_slider.setActiveMenu(0);
			}
			else if ($body.hasClass("account_templates"))
			{
				settingspage_slider.setActiveMenu(1);
			}
			else if ($body.hasClass("account_signatures"))
			{
				settingspage_slider.setActiveMenu(2);
			}
			else if ($body.hasClass("account_silentmode"))
			{
				settingspage_slider.setActiveMenu(3);
			}
			else if ($body.hasClass("account_accountlist"))
			{
				settingspage_slider.setActiveMenu(4);
			}
		}
		else if($body.hasClass("module_editaccount"))
		{
			var classes = strClasses.split(" ");
			var accountID = null;
			if(!$body.hasClass("newaccount"))
			{
				for(var i = 0; i < classes.length; i++)
				{
					if(classes[i].match(/^acc[0-9]/))
					{
						accountID = classes[i].replace("acc","");
					}
				}
			}
			onEditAccountEnter(accountID);
		}
		
		if(!is_conv)
			Conversations.Current = 0;
			
		resize_stuff();
	};
	
	
	
	
	
	ComSystem.Events.OnMissingData = function(){alert("missingdata")};
	ComSystem.Events.OnInvalidData = function(){alert("invaliddata")};
	ComSystem.Events.OnSystemOff = function(){alert("systemoff")};
	ComSystem.Events.OnUnknownError = function(){alert("unknown error")};
	ComSystem.Events.OnKick = function()
	{
		NavigationSystem.LoggedOut();
		setCookie("loggedin","false");
		alert("Du har blivit kickad");
	};
	
	
	
	function clearApp()
	{
		$("#conversation_list .conversation-item").remove();
		$("#convname_disp").html();
		$(".messagerow").remove();
	}
	
	
	
	function searchChange()
	{
		setTimeout(Conversations.SearchChange(),50);
	}

	</script>

    </body>
</html>
