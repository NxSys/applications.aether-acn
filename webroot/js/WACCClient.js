/**
 * WACCClient.js
 * $Id$
 *
 * The client console object for WACC
 *
 * @package WACC
 * @license https://nxsys.org/spaces/wacc/wiki/License
 * Please see the license.txt file or the url above for full copyright and
 * license terms.
 * @copyright Copyright 2013-2015 Nexus Systems, Inc.
 *
 * @author Robin Klingsberg <rklingsberg@f2developments.com>
 * @author $LastChangedBy$
 *
 * @version $Revision$
 * 
 * @deprecated Use WACCTerminal.js
 */

/**
 * The object
 *
 * @param String s_ServerPath The path to the server-side controller (must be on the same server as the JSConsole was loaded from to avoid XSS countermeasures)
 * @param boolean b_IsHidden (optional) Whether the console loads hidden or not. Defaults to false. If 'true', call toggleConsole to show/hide the console
 * @param String s_ParentID (optional) The ID of the console's parent element. Defaults to the body of the page.
 * @param String s_PromptText (optional) The text to display as the prompt. Defaults to 'WACC}'.
 * @param String i_ConsoleHeight (optional) The maximum height of the console. Defaults to the max-height of the console parent or if that is undefined, $(window).height().
 * @param boolean b_OverrideBrowserShortcuts (optional) Whether to override browser key combinations, such as Ctrl + L. Defaults to false.
 */
function WACCClient(s_ServerPath, b_IsHidden, s_ParentID, s_PromptText, i_ConsoleHeight, b_OverrideBrowserShortcuts)
{
  // All of this procedural code serves as the construct function

  this.s_ServerPath = s_ServerPath;

  if (typeof s_PromptText === 'undefined')
  {
    s_PromptText = 'WACC}';
  }

  this.s_PromptText = s_PromptText;

  this.s_ParentID = (typeof s_ParentID === 'undefined' || s_ParentID === '')? '' : s_ParentID;
  this.i_ConsoleHeight = (isNaN(i_ConsoleHeight)) ? undefined : i_ConsoleHeight;

  this.b_OverrideBrowserShortcuts = Boolean(b_OverrideBrowserShortcuts);

  this.s_CurrentCommand = '';
  this.a_lastResponse = [];
  this.i_HistoryPointer = 0;
  // @todo: Load configuration from cookie
  this.a_Configuration = [];
  // @todo: Load SID from server
  this.s_SID = '0000';
  this.a_History = [];
  self = this; // hack to support class-level members

  // Build console

  this.o_Console = $('<div/>', {id: 'wacc-console'});

  if (b_IsHidden)
  {
    this.o_Console.hide();
  }

  // Build config overlay (it is appended to the console parent in appendConsole())

  o_ConfigForm = $('<form/>', {id: 'wacc-config-form'});

  o_ConsoleStyleSelect = $('<select/>', {id: 'console-style'}).change(
    function ()
    {
      self.a_Configuration['console-style'] = $(this).val();
    }
  );
  o_ConsoleStyleSelect.append($('<option/>', {value: 'linux', selected: 'selected'}).text('Linux'));
  o_ConsoleStyleSelect.append($('<option/>', {value: 'windows'}).text('Windows'));
  //o_ConsoleStyleSelect.append($('<option/>', {value: 'ios'}).text('Cisco IOS'));

  o_ConfigForm.append($('<label for="console-style"/>').text('Console Style: '));
  o_ConfigForm.append(o_ConsoleStyleSelect);

  this.a_ConsoleColors = [];
  this.a_ConsoleColors['amber'] = '#ff7e00';
  this.a_ConsoleColors['green'] = '#00ff00';
  this.a_ConsoleColors['light-blue'] = '#0080ff';
  this.a_ConsoleColors['blue'] = '#0000ff';
  this.a_ConsoleColors['red'] = '#ff0000';
  this.a_ConsoleColors['white'] = '#ffffff';
  this.a_ConsoleColors['light-grey'] = '#898989';
  this.a_ConsoleColors['dark-grey'] = '#090909';
  this.a_ConsoleColors['black'] = '#000000';

  this.a_ConsoleFontSizes = ['x-small', 'small', 'medium', 'large', 'x-large'];

  o_TextColorSelect = $('<select/>', {id: 'text-color'}).change(
    function ()
    {
      self.o_Console.css('color', self.a_ConsoleColors[$(this).val()]);
	  self.o_CommandLine.css('color', self.a_ConsoleColors[$(this).val()]); //@see: IssueID #58
    }
  );
  for (index in this.a_ConsoleColors)
  {
    o_Option = $('<option/>', {value: index}).append($('<div/>').addClass('colorbox').css('background-color', this.a_ConsoleColors[index]).text(index));
    o_TextColorSelect.append(o_Option);
    if (index == 'light-blue')
    {
      o_Option.attr('selected', 'selected');
    }
  }

  o_ConfigForm.append($('<label for="text-color"/>').text('Text Color: '));
  o_ConfigForm.append(o_TextColorSelect);

  o_BkgColorSelect = $('<select/>', {id: 'bkg-color'}).change(
    function ()
    {
      self.o_Console.css('background-color', self.a_ConsoleColors[$(this).val()]);
    }
  );
  for (index in this.a_ConsoleColors)
  {
    o_Option = $('<option/>', {value: index}).append($('<div/>').addClass('colorbox').css('background-color', this.a_ConsoleColors[index]).text(index));
    o_BkgColorSelect.append(o_Option);
    if (index == 'dark-grey')
    {
      o_Option.attr('selected', 'selected');
    }
  }

  o_ConfigForm.append($('<label for="bkg-color"/>').text('Background Color: '));
  o_ConfigForm.append(o_BkgColorSelect);

  o_FontSizeSelect = $('<select/>', {id: 'font-size'}).change(
    function ()
    {
      self.o_Console.css('font-size', $(this).val());
    }
  );
  for (index in this.a_ConsoleFontSizes)
  {
    o_Option = $('<option/>', {value: this.a_ConsoleFontSizes[index]}).text(this.a_ConsoleFontSizes[index]);
    o_FontSizeSelect.append(o_Option);
    if (this.a_ConsoleFontSizes[index] == 'small')
    {
      o_Option.attr('selected', 'selected');
    }
  }

  o_ConfigForm.append($('<label for="font-size"/>').text('Font Size: '));
  o_ConfigForm.append(o_FontSizeSelect);

  this.o_ConfigOverlay = $('<div/>', {id: 'wacc-config-overlay'}).hide();
  this.o_ConfigOverlay.text('WACC Configuration');
  this.o_ConfigOverlay.append(o_ConfigForm);

  // Build console

  this.o_HistoryContainer = $('<div/>', {id: 'history'});

  this.o_Console.append(this.o_HistoryContainer);

  this.o_CommandLine = $('<input/>', {id: 'command-line', type: 'text'});
  this.o_CommandLine.keydown(
    function (event)
    {
      self.inputCommand(event);
    }
  );
  this.o_Console.click(
    function ()
    {
      self.o_CommandLine.focus();
    }
  );

  o_InputContainer = $('<div/>', {id: 'input'});
  o_Prompt = $('<label id="prompt" for="command-line"/>');
  o_Prompt.text(this.s_PromptText);
  this.i_PromptWidth = o_Prompt.width();
  o_InputContainer.append(o_Prompt);
  o_InputContainer.append(this.o_CommandLine);

  this.o_Console.append(o_InputContainer);

  // document.ready() is necessary to ensure that the parent element has been created when the console is appended to it
  $(document).ready(
    function ()
    {
      self.appendConsole();
      self.setConsoleHeight();
    }
  );

  // end construct code


  /**
   * Inserts the console into the page and sets focus on it
   */
  this.appendConsole = function ()
  {
    if (this.s_ParentID === '')
    {
      this.o_Parent = $('body');
    }
    else
    {
      if (this.s_ParentID.charAt(0) != '#')
	  {
	    this.s_ParentID = '#' + this.s_ParentID;
	  }
      this.o_Parent = $(this.s_ParentID);
    }

    this.o_Parent.css('overflow', 'auto');
    this.o_Parent.append(this.o_ConfigOverlay);
    this.o_Parent.append(this.o_Console);
    this.o_Parent.load(this.o_CommandLine.focus());
  };


  /**
   * Sets the console's max height before it scrolls
   */
  this.setConsoleHeight = function ()
  {
    if (typeof this.i_ConsoleHeight === 'undefined')
    {
      if (this.o_Parent.css('max-height') != 'none')
      {
        i_ParentHeight = parseInt(this.o_Parent.css('max-height'), 10);
      }
      else
      {
        //@todo FIXME This does not account for anything other visible elements in the window
        i_ParentHeight = $(window).height();
      }

      this.o_Console.height(i_ParentHeight);
    }
    else
	{
		this.o_Console.height(this.i_ConsoleHeight);
	}
  };


  /**
   * Shows and hides the console
   */
  this.toggleConsole = function ()
  {
    this.o_Console.slideToggle('fast',
      function ()
      {
        if (self.o_CommandLine.is(':visible'))
        {
          i_ConsoleWidth = self.o_Console.width();
          self.o_CommandLine.width((i_ConsoleWidth - self.i_PromptWidth));
          self.o_CommandLine.focus();
        }
        else if (self.o_ConfigOverlay.is(':visible'))
        {
          self.toggleOverlay();
        }
      }
    );
  };

  /**
   * Shows, hides, and centers the overlay over the console
   */
  this.toggleOverlay = function ()
  {
    // Calculated CSS style for centering the overlay
    n_HalfConsoleWidth = self.o_Console.width() / 2;
    n_HalfOverlayWidth = self.o_ConfigOverlay.width() / 2;
    i_ConsoleOffset = self.o_Console.offset();

    self.o_ConfigOverlay.css('top', (i_ConsoleOffset.top + 10));
    self.o_ConfigOverlay.css('left', (n_HalfConsoleWidth - n_HalfOverlayWidth));
    this.o_ConfigOverlay.slideToggle('fast');
  };


  /**
   * Handles the keypress event for the command line
   */
  this.inputCommand = function (o_Event)
  {
    switch (o_Event.which)
    {
      case 13: // return
        this.s_CurrentCommand = this.o_CommandLine.val();
        this.shufflePrompt();
        this.shuffleHistory();
        this.processCommand();
        break;
      case 38: // arrow up
        this.historyPrevious();
        o_Event.preventDefault();// this keeps the cursor from moving to the front of the input box
        break;
      case 40: // arrow down
        this.historyNext();
        break;
      case 220: // the '\' key
        if (this.n_PrevKeypress == 17)// Ctrl (so key combo is Ctrl+\)
        {
          this.toggleOverlay();
        }
        break;
	  case 76: // the 'l' key
		if (this.b_OverrideBrowserShortcuts && this.n_PrevKeypress == 17) // Ctrl (so key combo is Ctrl+L, or clear screen)
		{
			this.clearHistory();
			o_Event.preventDefault();
		}
        break;
      default:
        break;
    }
    // for multiple-key combos
    this.n_PrevKeypress = event.which;
  };

  /**
   * Processes the current command
   */
  this.processCommand = function ()
  {
    // some commands are handled locally
    switch (this.s_CurrentCommand)
    {
      case 'history':
        this.printHistory();
		break;
      case 'exit':
        //@todo this requires hiding the console parent. It would be nice to cause a chrome-appified WACC close on 'exit'
        break;
      case 'clear':
      case 'cls':
        self.clearHistory();
        break;
      default:
        if (this.s_CurrentCommand.length > 0)// because people like hitting enter
        {
          // away we go
		  this.s_RequestPath = this.s_ServerPath;
          //s_RequestPath = this.s_ServerPath +  '/' + this.s_SID + '/' + encodeURIComponent(this.s_CurrentCommand);
		  aSvrPayload={cmd: this.s_CurrentCommand, sid: this.s_SID};
		  $.ajax(this.s_RequestPath,
					{dataType: 'json',
					 data: aSvrPayload,
					 type: 'POST'
					}).success(
      			function (a_ResponseData)
      			{
                this.a_lastResponse=a_ResponseData;
                self.processResponse(a_ResponseData);
      			}
      		  ).error(
            function (o_XHR)
            {
                self.processResponseError(o_XHR);
            }
          );
        }
        break;
    }
  };

  /**
   * Processes a successful AJAX command response
   */
  this.processResponse = function (a_ResponseData)
  {
    o_ResponseDiv = $('<div/>');

    switch (a_ResponseData['code'])
    {
      case 0:
        break;
      default:
        o_ResponseDiv.addClass('error');
        break;
    }

    o_ResponseDiv.html(a_ResponseData['output']);

    this.o_HistoryContainer.append(o_ResponseDiv);

    this.scrollConsole();
  };


  /**
   * Processes an unsuccessful AJAX command response
   */
  this.processResponseError = function (o_XHR)
  {
    o_ResponseDiv = $('<div class="error"/>');

    //@todo deal with each HTTP status code properly. (ex: 403->login screen)

    if (299 < o_XHR.status < 499)
    {
      o_ResponseDiv.text('Server is unavailable to process commands. Please check your network connection and try again.');
    }
    else if (499 < o_XHR.status < 599)
    {
      o_ResponseDiv.text('Server error. Please reload the console and try again.');
    }

    this.o_HistoryContainer.append(o_ResponseDiv);

    this.scrollConsole();
  };


  /**
   * Performs necessary functions to the prompt after a command is entered
   */
  this.shufflePrompt = function ()
  {
    // keep history from getting polluted with a bunch of blank lines
    if (this.s_CurrentCommand.length > 0)
	{
		this.a_History.push(this.s_CurrentCommand);
	}

     // move the prompt command to the history list
    this.o_HistoryContainer.append($('<div/>').text(this.s_PromptText + this.s_CurrentCommand));

    // blank the prompt
    this.o_CommandLine.val('');

    this.scrollConsole();
  };


  /**
   * Keeps the console scrolled to the bottom. Must be called whenever anything is written into the console
   *
   * @todo see if it is possible to bind this function to a 'just got a new child' event of o_HistoryContainer
   */
  this.scrollConsole = function ()
  {
    // the [0] accesses the base DOM element
    this.o_Parent.scrollTop(this.o_Console[0].scrollHeight);
  };


  //
  // History Functions
  //


  /**
   * Moves the history pointer to the correct position when a command is sent
   */
  this.shuffleHistory = function ()
  {
    switch (this.a_Configuration['console-style'])
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
  };


  /**
   * Recalls the last command in the command history and displays it in the prompt
   */
  this.historyPrevious = function ()
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
  };


  /**
   * Recalls the next command in the command history and displays it in the prompt
   */
  this.historyNext = function ()
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
  };


  /**
   * Prints the entire command history
   */
  this.printHistory = function ()
  {
    for (index in this.a_History)
    {
      this.o_HistoryContainer.append($('<div/>').text(this.a_History[index]));
    }
  };

  this.clearHistory = function ()
  {
	  this.o_HistoryContainer.children(':visible').hide();
  };
}