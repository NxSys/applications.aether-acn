var WACCTerminal = (function () {
    function WACCTerminal(s_Route, s_TerminalID) {
        if (typeof s_Route === "undefined") { s_Route = ''; }
        if (typeof s_TerminalID === "undefined") { s_TerminalID = ''; }
        this.s_ParentID = '';
        this.s_PromptText = 'WACC}';
        this.s_DefaultBkgColor = 'dark-grey';
        this.s_DefaultTextColor = 'light-blue';
        this.b_IsHidden = false;
        this.b_OverrideBrowserCombos = false;
        this.o_Config = {
            terminalstyle: 'Linux',
            route: '',
            SID: 0
        };
        this.a_TerminalFontSizes = [
            'x-small', 
            'small', 
            'medium', 
            'large', 
            'x-large'
        ];
        this.o_TerminalColors = {
            'amber': '#ff7e00',
            'green': '#00ff00',
            'light-blue': '#0080ff',
            'blue': '#0000ff',
            'red': '#ff0000',
            'white': '#ffffff',
            'light-grey': '#898989',
            'dark-grey': '#090909',
            'black': '#000000'
        };
        this.i_PromptWidth = 0;
        this.s_CurrentCommand = '';
        this.o_lastResponse = {
        };
        this.i_HistoryPointer = 0;
        this.a_History = [];
        this.b_Ready = false;
        var self = this;
        $(document).ready(function () {
            if(typeof s_Route !== 'undefined' && s_Route !== '') {
                self.o_Config.route = s_Route;
            } else {
                self.o_Config.route = '';
            }
            if(typeof s_TerminalID !== 'undefined' && s_TerminalID !== '') {
                self.o_Terminal = $('#' + s_TerminalID);
            } else {
                self.o_Terminal = $('#wacc-terminal');
            }
            if(0 == self.o_Terminal.length) {
                self.o_Terminal = $('<div id="wacc-terminal"/>');
                $('body').append(self.o_Terminal);
            }
            if(self.b_IsHidden) {
                self.o_Terminal.hide();
            }
            self.o_Parent = self.o_Terminal.parent();
            self.o_ConfigOverlay = $('<div id="wacc-config-overlay"/>').hide();
            self.o_ConfigOverlay.load('templates/overlay.html', function () {
                self.configureOverlay();
            });
            self.o_Terminal.load('templates/terminal.html', function () {
                self.configureTerminal();
                $.event.trigger({
                    type: 'WACCTerminal_Ready'
                });
            });
        });
    }
    WACCTerminal.prototype.configureOverlay = function () {
        var self = this;
        this.o_ConfigOverlay.find('#terminal-style').change(function () {
            self.o_Config.terminalstyle = $(this).val();
        });
        var o_TextColorSelect = this.o_ConfigOverlay.find('#text-color');
        o_TextColorSelect.change(function () {
            var s_Color = $(this).val();
            self.o_Terminal.css('color', self.o_TerminalColors[s_Color]);
            self.o_CommandLine.css('color', self.o_TerminalColors[s_Color]);
        });
        var o_BkgColorSelect = this.o_ConfigOverlay.find('#bkg-color');
        o_BkgColorSelect.change(function () {
            var s_Color = $(this).val();
            self.o_Terminal.css('background-color', self.o_TerminalColors[s_Color]);
        });
        for(var index in this.o_TerminalColors) {
            var o_TxtColorOption = $('<option/>', {
                value: index
            }).append($('<div/>').addClass('colorbox').css('background-color', this.o_TerminalColors[index]).text(index));
            if(index == this.s_DefaultTextColor) {
                o_TxtColorOption.attr('selected', 'selected');
            }
            o_TextColorSelect.append(o_TxtColorOption);
            var o_BkgColorOption = o_TxtColorOption.clone();
            if(index == this.s_DefaultBkgColor) {
                o_BkgColorOption.attr('selected', 'selected');
            }
            o_BkgColorSelect.append(o_BkgColorOption);
        }
        var o_FontSizeSelect = this.o_ConfigOverlay.find('#font-size');
        o_FontSizeSelect.change(function () {
            self.o_Terminal.css('font-size', $(this).val());
        });
        for(var index in this.a_TerminalFontSizes) {
            var o_Option = $('<option/>', {
                value: this.a_TerminalFontSizes[index]
            }).text(this.a_TerminalFontSizes[index]);
            if(this.a_TerminalFontSizes[index] == 'small') {
                o_Option.attr('selected', 'selected');
            }
            o_FontSizeSelect.append(o_Option);
        }
        this.o_Parent.append(this.o_ConfigOverlay);
    };
    WACCTerminal.prototype.configureTerminal = function () {
        this.o_HistoryContainer = this.o_Terminal.find('#history');
        this.o_CommandLine = this.o_Terminal.find('#command-line');
        this.o_Prompt = this.o_Terminal.find('#prompt');
        this.o_Terminal.css('color', this.o_TerminalColors[this.s_DefaultTextColor]);
        this.o_Terminal.css('background-color', this.o_TerminalColors[this.s_DefaultBkgColor]);
        this.o_HistoryContainer.css('color', this.o_TerminalColors[this.s_DefaultTextColor]);
        this.o_HistoryContainer.css('background-color', this.o_TerminalColors[this.s_DefaultBkgColor]);
        this.o_Prompt.css('color', this.o_TerminalColors[this.s_DefaultTextColor]);
        this.o_Prompt.css('background-color', this.o_TerminalColors[this.s_DefaultBkgColor]);
        this.o_CommandLine.css('color', this.o_TerminalColors[this.s_DefaultTextColor]);
        this.o_CommandLine.css('background-color', this.o_TerminalColors[this.s_DefaultBkgColor]);
        var self = this;
        this.o_CommandLine.keydown(function (o_Event) {
            self.processKeystroke(o_Event);
        });
        this.o_Terminal.click(function () {
            self.o_CommandLine.focus();
        });
        this.o_Prompt.text(this.s_PromptText);
        var i_PromptWidth = this.o_Prompt.width();
        var i_TerminalWidth = this.o_Terminal.width();
        this.o_CommandLine.width(i_TerminalWidth - i_PromptWidth - 5);
        this.o_Parent.css('overflow', 'auto');
        this.o_Parent.load(this.o_CommandLine.focus());
    };
    WACCTerminal.prototype.toggleTerminal = function () {
        this.o_Terminal.slideToggle('fast', function () {
            if(this.o_CommandLine.is(':visible')) {
                this.o_CommandLine.focus();
            } else if(this.o_ConfigOverlay.is(':visible')) {
                this.toggleConfigurationOverlay();
            }
        });
    };
    WACCTerminal.prototype.toggleConfigurationOverlay = function () {
        var n_HalfTerminalWidth = this.o_Terminal.width() / 2;
        var n_HalfOverlayWidth = this.o_ConfigOverlay.width() / 2;
        var i_TerminalOffset = this.o_Terminal.offset();
        this.o_ConfigOverlay.css('top', (i_TerminalOffset.top + 10));
        this.o_ConfigOverlay.css('left', (n_HalfTerminalWidth - n_HalfOverlayWidth));
        this.o_ConfigOverlay.slideToggle('fast');
    };
    WACCTerminal.prototype.getHistory = function () {
        return this.a_History;
    };
    WACCTerminal.prototype.processKeystroke = function (o_Event) {
        switch(o_Event.which) {
            case 13:
                this.s_CurrentCommand = this.o_CommandLine.val();
                this.clearPrompt();
                this.repositionHistoryPointer();
                this.processCommand();
                break;
            case 38:
                this.previousCommand();
                o_Event.preventDefault();
                break;
            case 40:
                this.nextCommand();
                break;
            case 220:
                if(this.n_PrevKeypress == 17) {
                    this.toggleConfigurationOverlay();
                }
                break;
            case 76:
                if(this.b_OverrideBrowserCombos && this.n_PrevKeypress == 17) {
                    this.clearHistory();
                    o_Event.preventDefault();
                }
                break;
            case 67:
                if(this.n_PrevKeypress == 17) {
                    this.clearPrompt();
                }
                break;
            default:
                break;
        }
        this.n_PrevKeypress = o_Event.which;
    };
    WACCTerminal.prototype.processCommand = function (s_Command) {
        if (typeof s_Command === "undefined") { s_Command = ''; }
        s_Command = (typeof s_Command === 'undefined' || s_Command === '') ? this.s_CurrentCommand : s_Command;
        switch(s_Command) {
            case 'history':
                this.printHistory();
                break;
            case 'exit':
                break;
            case 'clear':
            case 'cls':
                this.clearHistory();
                break;
            default:
                if(s_Command.length > 0) {
                    var o_Payload = {
                        cmd: s_Command,
                        sid: this.o_Config.SID
                    };
                    this.sendCommand(o_Payload);
                }
                break;
        }
    };
    WACCTerminal.prototype.sendCommand = function (o_Payload) {
        var self = this;
        $.ajax(this.o_Config.route, {
            dataType: 'json',
            type: 'POST',
            data: o_Payload
        }).success(function (o_Response) {
            this.o_lastResponse = o_Response;
            var o_Response = self.processResponse(o_Response);
            self.printMessage(o_Response);
        }).error(function (o_XHR) {
            var o_Response = self.processResponseError(o_XHR);
            self.printMessage(o_Response);
        });
    };
    WACCTerminal.prototype.processResponse = function (o_Response) {
        var b_Success = false;
        switch(o_Response.code) {
            case 0:
                b_Success = true;
                break;
            default:
                break;
        }
        return {
            output: o_Response.output,
            success: b_Success
        };
    };
    WACCTerminal.prototype.processResponseError = function (o_XHR) {
        var s_Message;
        if(299 < o_XHR.status < 399) {
            s_Message = 'Server returned an unexpected response (' + o_XHR.status + '). Please check your configuration and try again.';
        } else if(400 === o_XHR.status) {
            s_Message = 'Server could not understand your request. Please check your client-side configuration and try again.';
        } else if(401 === o_XHR.status) {
            s_Message = 'This command requires authorization. Please authenticate using the login command.';
        } else if(403 === o_XHR.status) {
            s_Message = 'This command is not allowed.';
        } else if(404 === o_XHR.status) {
            s_Message = 'Server is unavailable to process commands. Please check your network connection and try again.';
        } else if(404 < o_XHR.status < 499) {
            s_Message = 'Server returned an unexpected response (' + o_XHR.status + '). Please check your configuration and try again.';
        } else if(499 < o_XHR.status < 599) {
            s_Message = 'Server error. Please reload the terminal and try again.';
        }
        return {
            output: s_Message,
            success: false
        };
    };
    WACCTerminal.prototype.printMessage = function (o_Message) {
        var o_ResponseDiv = $('<div/>');
        if(!o_Message.success) {
            o_ResponseDiv.addClass('error');
        }
        o_ResponseDiv.html(o_Message.output);
        this.o_HistoryContainer.append(o_ResponseDiv);
        this.scrollTerminal();
    };
    WACCTerminal.prototype.clearPrompt = function () {
        if(this.s_CurrentCommand.length > 0) {
            this.a_History.push(this.s_CurrentCommand);
        }
        var o_Prompt = $('<div class="prompt"/>').text(this.s_PromptText).height(this.o_CommandLine.height());
        var o_CommandLine = $('<div class="command-line"/>').text(this.o_CommandLine.val()).width(this.o_CommandLine.width()).height(this.o_CommandLine.height());
        var o_HistoryLine = $('<div class="input"/>').height(this.o_CommandLine.height());
        o_HistoryLine.append(o_Prompt);
        o_HistoryLine.append(o_CommandLine);
        this.o_HistoryContainer.append(o_HistoryLine);
        this.o_CommandLine.val('');
        this.scrollTerminal();
    };
    WACCTerminal.prototype.scrollTerminal = function () {
        this.o_Parent.scrollTop(this.o_Terminal[0].scrollHeight);
    };
    WACCTerminal.prototype.repositionHistoryPointer = function () {
        switch(this.o_Config.terminalstyle) {
            case 'windows':
                if(this.i_HistoryPointer < (this.a_History.length - 1)) {
                    this.i_HistoryPointer++;
                }
                break;
            case 'linux':
            default:
                this.i_HistoryPointer = this.a_History.length - 1;
                break;
        }
    };
    WACCTerminal.prototype.previousCommand = function () {
        if(this.i_HistoryPointer == (this.a_History.length - 1)) {
            this.s_CurrentCommand = this.o_CommandLine.val();
        }
        if(this.i_HistoryPointer >= 0) {
            var s_PreviousCommand = this.a_History[this.i_HistoryPointer];
            this.o_CommandLine.val(s_PreviousCommand);
            if(this.i_HistoryPointer > 0) {
                this.i_HistoryPointer--;
            }
        }
    };
    WACCTerminal.prototype.nextCommand = function () {
        if(this.i_HistoryPointer < (this.a_History.length - 1)) {
            this.i_HistoryPointer++;
            var s_NextCommand = this.a_History[this.i_HistoryPointer];
            this.o_CommandLine.val(s_NextCommand);
        } else if(this.i_HistoryPointer == (this.a_History.length - 1)) {
            this.o_CommandLine.val(this.s_CurrentCommand);
        }
    };
    WACCTerminal.prototype.printHistory = function () {
        for(var index in this.a_History) {
            this.o_HistoryContainer.append($('<div/>').text(this.a_History[index]));
        }
    };
    WACCTerminal.prototype.clearHistory = function () {
        this.o_HistoryContainer.children(':visible').hide();
    };
    return WACCTerminal;
})();
