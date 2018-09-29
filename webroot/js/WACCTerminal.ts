/**
 * WACCTerminal.ts
 * $Id
 *
 * The terminal client for WACC, now in TypeScript!
 *
 * @package WACC
 * @license http://f2dev.com/prjs/wacc/license.html
 * Please see the license.txt file or the url above for full copyright and license information.
 * @copyright Copyright 2013 F2 Developments, Inc.
 *
 * @author Robin Klingsberg <rklingsberg@f2developments.com>
 * @author $LastChangedBy
 *
 * @version $Revision
 */

class WACCTerminal
{
	/* Public member variables */

	/**
	* @var string s_ParentID The ID of the terminal's parent element. Defaults to the body of the page.
	*/
	public s_ParentID: string = '';

	/**
	* @var string s_PromptText The text to display as the prompt. Defaults to 'WACC}'.
	*/
	public s_PromptText: string = 'WACC}';

	/**
	* @var string s_DefaultBkgColor Default background color of WACCTerminal. Must be defined in o_TerminalColors.
	*/
	public s_DefaultBkgColor: string = 'dark-grey';

	/**
	* @var string s_DefaultTextColor Default text color of WACCTerminal. Must be defined in o_TerminalColors.
	*/
	public s_DefaultTextColor: string = 'light-blue';

	/**
	* @var integer i_TerminalHeight The maximum height of the terminal. Defaults to the max-height of the terminal parent or if that is undefined, 300.
	*/
	public i_TerminalHeight: number;

	/**
	* @var boolean b_IsHidden Whether the terminal starts hidden or not. Defaults to false. If you set this to true, call toggleTerminal to show/hide the terminal
	*/
	public b_IsHidden: bool = false;

	/**
	* @var boolean b_OverrideBrowserCombos Whether to override browser key combinations, such as Ctrl + L. Defaults to false.
	*/
	public b_OverrideBrowserCombos: bool = false;

	/* Private member variables */

	// Configuration
	private o_Config =
	{
		terminalstyle	: 'Linux',
		route			: '',
		SID 			: 0000
	};
	private a_TerminalFontSizes = ['x-small', 'small', 'medium', 'large', 'x-large'];
	private o_TerminalColors =
	{
		'amber'		:	'#ff7e00',
		'green' 	:	'#00ff00',
		'light-blue':	'#0080ff',
		'blue'		:	'#0000ff',
		'red'		:	'#ff0000',
		'white'		:	'#ffffff',
		'light-grey':	'#898989',
		'dark-grey' :	'#090909',
		'black'		:	'#000000'
	};

	// DOM elements
	private o_ConfigOverlay;
	private o_Terminal;
	private o_Parent;
	private o_Prompt;
	private o_CommandLine;
	private o_HistoryContainer;

	// Config
	private i_PromptWidth: number = 0;
	private s_CurrentCommand: string = '';
	private o_lastResponse = {};
	private i_HistoryPointer: number = 0;
	private n_PrevKeypress: number;
	private a_History = [];
	private b_Ready = false;

	/**
	 * @param String s_Route The route on the server that accepts WACC commands. Defaults to '/'.
	 */
	constructor(s_Route = '', s_TerminalID = '')
	{
		var self = this;
		// this ensures that we have access to the necessary elements
		$(document).ready(function() {
			if (typeof s_Route !== 'undefined' && s_Route !== '')
			{
				self.o_Config.route = s_Route;
			}
			else
			{
				self.o_Config.route = '';
			}

			if (typeof s_TerminalID !== 'undefined' && s_TerminalID !== '')
			{
				self.o_Terminal = $('#' + s_TerminalID);
			}
			else
			{
				self.o_Terminal = $('#wacc-terminal');
			}

			if (0 == self.o_Terminal.length)
			{
				self.o_Terminal = $('<div id="wacc-terminal"/>');
				$('body').append(self.o_Terminal);
			}

			if (self.b_IsHidden)
			{
				self.o_Terminal.hide();
			}

			self.o_Parent = self.o_Terminal.parent();

			// @todo: Load configuration from cookie
			// @todo: Load SID from server

			// Load config template
			self.o_ConfigOverlay = $('<div id="wacc-config-overlay"/>').hide();
			self.o_ConfigOverlay.load('templates/overlay.html', function(){
				self.configureOverlay();
			});

			// Load terminal template
			self.o_Terminal.load('templates/terminal.html', function(){
				self.configureTerminal();
				$.event.trigger({type: 'WACCTerminal_Ready'});
			});
		});
	}

	public configureOverlay()
	{
		var self = this;
		this.o_ConfigOverlay.find('#terminal-style').change(function() {
			self.o_Config.terminalstyle = $(this).val();
		});

		var o_TextColorSelect = this.o_ConfigOverlay.find('#text-color');
		
		o_TextColorSelect.change(function() {
			var s_Color = $(this).val();
			self.o_Terminal.css('color', self.o_TerminalColors[s_Color]);
			self.o_CommandLine.css('color', self.o_TerminalColors[s_Color]);
		});

		var o_BkgColorSelect = this.o_ConfigOverlay.find('#bkg-color');

		o_BkgColorSelect.change(function() {
			var s_Color = $(this).val();
			self.o_Terminal.css('background-color', self.o_TerminalColors[s_Color]);
		});

		for (var index in this.o_TerminalColors)
		{
			var o_TxtColorOption = $('<option/>', {value: index}).append($('<div/>').addClass('colorbox').css('background-color', this.o_TerminalColors[index]).text(index));
			if (index == this.s_DefaultTextColor)
			{
				o_TxtColorOption.attr('selected', 'selected');
			}
			o_TextColorSelect.append(o_TxtColorOption);

			var o_BkgColorOption = o_TxtColorOption.clone();
			if (index == this.s_DefaultBkgColor)
			{
			  o_BkgColorOption.attr('selected', 'selected');
			}
			o_BkgColorSelect.append(o_BkgColorOption);
		}

		var o_FontSizeSelect = this.o_ConfigOverlay.find('#font-size');

		o_FontSizeSelect.change(function() {
			self.o_Terminal.css('font-size', $(this).val());
		});

		for (var index in this.a_TerminalFontSizes)
		{
			var o_Option = $('<option/>', {value: this.a_TerminalFontSizes[index]}).text(this.a_TerminalFontSizes[index]);
			if (this.a_TerminalFontSizes[index] == 'small')
			{
			  o_Option.attr('selected', 'selected');
			}
			o_FontSizeSelect.append(o_Option);
		}

		this.o_Parent.append(this.o_ConfigOverlay);
	}

	public configureTerminal()
	{
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
		this.o_CommandLine.keydown(function(o_Event) {
			self.processKeystroke(o_Event);
		});

		this.o_Terminal.click(function() {
			self.o_CommandLine.focus();
		});

		this.o_Prompt.text(this.s_PromptText);
		var i_PromptWidth = this.o_Prompt.width();
		var i_TerminalWidth = this.o_Terminal.width();
		this.o_CommandLine.width(i_TerminalWidth - i_PromptWidth - 5);// #prompt has a 3px right margin

		this.o_Parent.css('overflow', 'auto');
		// onLoad handler
		this.o_Parent.load(this.o_CommandLine.focus());
	}

	/**
	* Shows and hides the terminal
	*/
	public toggleTerminal()
	{
		this.o_Terminal.slideToggle('fast',
		function()
		{
			if (this.o_CommandLine.is(':visible'))
			{
				this.o_CommandLine.focus();
			}
			else if (this.o_ConfigOverlay.is(':visible'))
			{
				this.toggleConfigurationOverlay();
			}
		});
	}

	/**
	* Shows, hides, and centers the configuration overlay over the terminal
	*/
	public toggleConfigurationOverlay()
	{
		// Calculated CSS style for centering the overlay
		var n_HalfTerminalWidth = this.o_Terminal.width() / 2;
		var n_HalfOverlayWidth = this.o_ConfigOverlay.width() / 2;
		var i_TerminalOffset = this.o_Terminal.offset();

		this.o_ConfigOverlay.css('top', (i_TerminalOffset.top + 10));
		this.o_ConfigOverlay.css('left', (n_HalfTerminalWidth - n_HalfOverlayWidth));
		this.o_ConfigOverlay.slideToggle('fast');
	}

	public getHistory()
	{
		return this.a_History;
	}

	/**
   	* Handles the keypress event for the command line
   	*/
	private processKeystroke(o_Event)
	{
		switch (o_Event.which)
		{
			case 13: // return
				this.s_CurrentCommand = this.o_CommandLine.val();
				this.clearPrompt();
				this.repositionHistoryPointer();
				this.processCommand();
				break;
			case 38: // arrow up
				this.previousCommand();
				o_Event.preventDefault();// this keeps the cursor from moving to the front of the input box
				break;
			case 40: // arrow down
				this.nextCommand();
				break;
			case 220: // the '\' key
				if (this.n_PrevKeypress == 17)// Ctrl (so key combo is Ctrl+\)
				{
					this.toggleConfigurationOverlay();
				}
				break;
			case 76: // the 'l' key
				if (this.b_OverrideBrowserCombos && this.n_PrevKeypress == 17) // Ctrl (so key combo is Ctrl + L, or clear screen)
				{
					this.clearHistory();
					o_Event.preventDefault();
				}
				break;
			case 67: // the 'c' key
				if (this.n_PrevKeypress == 17) // Ctrl (so key combo is Ctrl + C, or Cancel)
				{
					this.clearPrompt();
				}
				break;
			default:
				break;
		}
		// storing current keypress for the next iteration of this function (for two-key combos)
		this.n_PrevKeypress = o_Event.which;
	}

	/**
   	* Processes a command
   	*
   	* @param string s_Command The command to process. Defaults to this.s_CurrentCommand if none given.
   	*/
	public processCommand(s_Command = '')
	{
		s_Command = (typeof s_Command === 'undefined' || s_Command === '')? this.s_CurrentCommand : s_Command;
		// some commands are handled locally
		switch (s_Command)
		{
			case 'history':
				this.printHistory();
				break;
			case 'exit':
				//@todo this requires hiding the terminal parent. It would be nice to cause a chrome-appified WACC close on 'exit'
				break;
			case 'clear':
			case 'cls':
				this.clearHistory();
				break;
			default:
				if (s_Command.length > 0)// because people like hitting enter
				{
					var o_Payload = {cmd: s_Command, sid: this.o_Config.SID};
					this.sendCommand(o_Payload);
				}
				break;
		}
	}

	private sendCommand(o_Payload)
	{
		// this is necessary to call WACCTerminal functions from within the AJAX object created below
		var self = this;
		$.ajax(this.o_Config.route,
		{
			dataType: 'json',
			type: 'POST',
			data: o_Payload
		})
		.success(function(o_Response) {
			this.o_lastResponse = o_Response;
			var o_Response = self.processResponse(o_Response);
			self.printMessage(o_Response);
		})
		.error(function(o_XHR) {
			var o_Response = self.processResponseError(o_XHR);
			self.printMessage(o_Response);
		});
	}
	
	/**
   	* Processes a successful AJAX command response
   	*/
	private processResponse(o_Response)
	{
		var b_Success = false;
		switch (o_Response.code)
		{
			case 0:
				b_Success = true;
				break;
			default:
			break;
		}

		return {output: o_Response.output, success: b_Success};
	}

	/**
	* Processes an unsuccessful AJAX command response
	*/
	private processResponseError(o_XHR)
	{
		var s_Message: string;

		// The 300s
		if (299 < o_XHR.status < 399)
		{
			s_Message = 'Server returned an unexpected response (' + o_XHR.status + '). Please check your configuration and try again.';
		}

		// The 400s WACCserver might return under normal circumstances
		else if (400 === o_XHR.status)
		{
			s_Message = 'Server could not understand your request. Please check your client-side configuration and try again.';
		}
		else if (401 === o_XHR.status)
		{
			s_Message = 'This command requires authorization. Please authenticate using the login command.';
			// @todo chain to login
		}
		else if (403 === o_XHR.status)
		{
			s_Message = 'This command is not allowed.';
		}
		else if (404 === o_XHR.status)
		{
			s_Message = 'Server is unavailable to process commands. Please check your network connection and try again.';
		}

		// The rest of the 400s
		else if (404 < o_XHR.status < 499)
		{
			s_Message = 'Server returned an unexpected response (' + o_XHR.status + '). Please check your configuration and try again.';
		}

		// The 500s
		else if (499 < o_XHR.status < 599)
		{
			s_Message = 'Server error. Please reload the terminal and try again.';
		}

		return {output : s_Message, success : false};
	}

	/**
	* Prints a message to the terminal
	*/
	private printMessage(o_Message)
	{
		var o_ResponseDiv = $('<div/>');

		if (!o_Message.success)
		{
			o_ResponseDiv.addClass('error');
		}

		o_ResponseDiv.html(o_Message.output);

		this.o_HistoryContainer.append(o_ResponseDiv);

		this.scrollTerminal();
	}

	/**
   	* Moves the current command to history, clears, and focuses on the prompt after a command is entered
   	*/
	private clearPrompt()
	{
		// keep history from getting polluted with a bunch of blank lines
		if (this.s_CurrentCommand.length > 0)
		{
			this.a_History.push(this.s_CurrentCommand);
		}

		// move a record of the prompt command to the history list
		var o_Prompt = $('<div class="prompt"/>').text(this.s_PromptText).height(this.o_CommandLine.height());
		var o_CommandLine = $('<div class="command-line"/>').text(this.o_CommandLine.val()).width(this.o_CommandLine.width()).height(this.o_CommandLine.height());
		var o_HistoryLine = $('<div class="input"/>').height(this.o_CommandLine.height());
		o_HistoryLine.append(o_Prompt);
		o_HistoryLine.append(o_CommandLine);

		this.o_HistoryContainer.append(o_HistoryLine);

		// blank the prompt
		this.o_CommandLine.val('');

		this.scrollTerminal();
	}

	/**
	* Keeps the terminal scrolled to the bottom. Must be called whenever anything is written into the terminal
	*
	* @todo see if it is possible to bind this function to a 'just got a new child' event of o_HistoryContainer
	*/
	private scrollTerminal()
	{
		// the [0] accesses the base DOM element
		this.o_Parent.scrollTop(this.o_Terminal[0].scrollHeight);
		//this.o_Parent.animate({scrollTop:this.o_Terminal.height()}, 1000);
		//this.o_Parent.scrollTop(this.o_Terminal.height());
	}

	//
	// History Functions
  	//

	/**
	* Moves the history pointer to the correct position when a command is sent
	*/
	private repositionHistoryPointer()
	{
		switch (this.o_Config.terminalstyle)
		{
			case 'windows':
				// @todo FIXME this does not work as intended
				if (this.i_HistoryPointer < (this.a_History.length - 1))// this advances the history pointer by one if a command in the history was used
				{
					this.i_HistoryPointer++;
				}
				break;
			/*case 'ios':
			break;*/
			case 'linux':
			default:
				this.i_HistoryPointer = this.a_History.length - 1; // this keeps the history pointer at the most current entry
				break;
		}
	}

	/**
	* Retrieves the last command in the history array and displays it in the prompt
	*/
	private previousCommand()
	{
		if (this.i_HistoryPointer == (this.a_History.length - 1))
		{
			// save the command in the prompt
			this.s_CurrentCommand = this.o_CommandLine.val();
		}

		if (this.i_HistoryPointer >= 0)
		{
			var s_PreviousCommand = this.a_History[this.i_HistoryPointer];
			this.o_CommandLine.val(s_PreviousCommand);

			if (this.i_HistoryPointer > 0)
			{
				this.i_HistoryPointer--;
			}
		}
	}

	/**
	* Retrieves the next command in the history and displays it in the prompt
	*/
	private nextCommand()
	{
		if (this.i_HistoryPointer < (this.a_History.length - 1))
		{
			this.i_HistoryPointer++;
			var s_NextCommand = this.a_History[this.i_HistoryPointer];
			this.o_CommandLine.val(s_NextCommand);
		}
		else if (this.i_HistoryPointer == (this.a_History.length - 1))// we're at the current command
		{
			this.o_CommandLine.val(this.s_CurrentCommand);
		}
	}

	/**
	* Prints out a list of all commands in the history array
	*/
	private printHistory()
	{
		for (var index in this.a_History)
		{
			this.o_HistoryContainer.append($('<div/>').text(this.a_History[index]));
		}
	}

	/**
	* Clears the WACCTerminal screen by hiding the contents of the history container
	*/
	private clearHistory()
	{
		this.o_HistoryContainer.children(':visible').hide();
	}
}