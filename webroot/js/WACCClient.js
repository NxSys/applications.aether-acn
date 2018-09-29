/**
 * WACCClient.js
 * $Id$
 *
 * The client console object for WACC
 *
 * @package WACC
 * @copyright © 2012-20XX F2 Developments, Inc. All rights reserved.
 * @license http://f2dev.com/prjs/prj/lic
 *
 * @author Robin Klingsberg <rklingsberg@f2developments.com>
 * @author $LastChangedBy$
 *
 * @version $Revision$
 */

/**
 * The object itself, as a construct function... fracking Javascript....
 *
 * @param String s_ServerPath The path to the server-side controller (must be on the same server as the JSConsole was loaded from to avoid XSS countermeasures)
 * @param boolean b_IsHidden (optional) Whether the console loads hidden or not. Defaults to false. If 'true', call f_ToggleConsole to show/hide the console
 * @param String s_ParentID (optional) The ID of the console's parent element. Defaults to the body of the page.
 * @param String s_PromptText (optional) The text to display as the prompt. Defaults to 'WACC>'.
 * @param String i_ConsoleHeight (optional) The maximum height of the console. Defaults to the max-height of the console parent or if that is undefined, $(window).height().
 */
function WACCClient(s_ServerPath, b_IsHidden, s_ParentID, s_PromptText, i_ConsoleHeight)
{
  // All of this procedural code serves as the construct function

  this.s_ServerPath = s_ServerPath;

  if (typeof s_PromptText === undefined)
  {
    s_PromptText = 'WACC> ';
  }
  else
  {
    if (s_PromptText.charAt((s_PromptText.length - 1)) != ' ') s_PromptText += ' ';
  }
  this.s_PromptText = s_PromptText;

  this.s_ParentID = (typeof s_ParentID === undefined || s_ParentID == '')? '' : s_ParentID;
  this.i_ConsoleHeight = (isNaN(i_ConsoleHeight)) ? undefined : i_ConsoleHeight

  this.s_CurrentCommand = '';
  this.i_HistoryPointer = 0;
  // @todo: Load configuration from cookie
  this.a_Configuration = new Array();
  // @todo: Load SID from server
  this.s_SID = '0000';
  this.a_History = new Array();;
  self = this; // this is the alternative to making global functions and vars... ick.


  // Build console

  this.o_Console = $('<div/>', {id: 'wacc-console'});

  if (b_IsHidden)
  {
    this.o_Console.hide();
  }

  // Build config overlay (it is appended to the console parent in f_AppendConsole())

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

  o_ConfigForm.append($('<label/>', {for: 'console-style'}).text('Console Style: '));
  o_ConfigForm.append(o_ConsoleStyleSelect);

  this.a_ConsoleColors = new Array();
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
    }
  );
  for (index in this.a_ConsoleColors)
  {
    o_Option = $('<option/>', {value: index}).append($('<div/>').addClass('colorbox').css('color', this.a_ConsoleColors[index]));
    o_TextColorSelect.append(o_Option);
    if (index == 'light-blue')
    {
      o_Option.attr('selected', 'selected');
    }
  }

  o_ConfigForm.append($('<label/>', {for: 'text-color'}).text('Text Color: '));
  o_ConfigForm.append(o_TextColorSelect);

  o_BkgColorSelect = $('<select/>', {id: 'bkg-color'}).change(
    function ()
    {
      self.o_Console.css('background-color', self.a_ConsoleColors[$(this).val()]);
    }
  );
  for (index in this.a_ConsoleColors)
  {
    o_Option = $('<option/>', {value: index}).append($('<div/>').addClass('colorbox').css('color', this.a_ConsoleColors[index]));
    o_BkgColorSelect.append(o_Option);
    if (index == 'dark-grey')
    {
      o_Option.attr('selected', 'selected');
    }
  }

  o_ConfigForm.append($('<label/>', {for: 'bkg-color'}).text('Background Color: '));
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

  o_ConfigForm.append($('<label/>', {for: 'font-size'}).text('Font Size: '));
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
      self.f_InputCommand(event);
    }
  );
  this.o_Console.click(
    function ()
    {
      self.o_CommandLine.focus();
    }
  );

  o_InputContainer = $('<div/>', {id: 'input'});
  o_InputContainer.append($('<label/>', {id: 'prompt', for: 'command-line'}).text(this.s_PromptText));
  o_InputContainer.append(this.o_CommandLine);

  this.o_Console.append(o_InputContainer);

  // document.ready() is necessary to ensure that the parent element has been created when the console is appended to it
  $(document).ready(
    function ()
    {
      self.f_AppendConsole();
      self.f_SetConsoleHeight();
    }
  );

  // end construct code


  /**
   * Inserts the console into the page and sets focus on it
   */
  this.f_AppendConsole = function ()
  {
    if (this.s_ParentID == '')
    {
      this.o_Parent = $('body');
    }
    else
    {
      if (this.s_ParentID.charAt(0) != '#') this.s_ParentID = '#' + this.s_ParentID;
      this.o_Parent = $(this.s_ParentID);
    }

    this.o_Parent.css('overflow', 'auto');
    this.o_Parent.append(this.o_ConfigOverlay);
    this.o_Parent.append(this.o_Console);
    this.o_Parent.load(this.o_CommandLine.focus());
  }


  /**
   * Sets the console's max height before it scrolls
   */
  this.f_SetConsoleHeight = function ()
  {
    if (typeof this.i_ConsoleHeight === undefined)
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
    else this.o_Console.height(this.i_ConsoleHeight);
  }


  /**
   * Shows and hides the console
   */
  this.f_ToggleConsole = function ()
  {
    this.o_Console.slideToggle('fast',
      function ()
      {
        if (self.o_CommandLine.is(':visible'))
          {
            self.o_CommandLine.focus();
          }
      }
    );
  }


  /**
   * Handles the keypress event for the command line
   */
  this.f_InputCommand = function (o_Event)
  {
    switch (o_Event.which)
    {
      case 13: // return
        this.s_CurrentCommand = this.o_CommandLine.val();
        this.f_ShufflePrompt();
        this.f_ShuffleHistory();
        this.f_ProcessCommand();
        break;
      case 38: // arrow up
        this.f_HistoryPrevious();
        o_Event.preventDefault();// this keeps the cursor from moving to the front of the input box
        break;
      case 40: // arrow down
        this.f_HistoryNext();
        break;
      case 220: // the '\' key
        if (this.n_PrevKeypress == 17)// Ctrl (so key combo is Ctrl+\)
        {
          this.o_ConfigOverlay.toggle();
        }
        break;
      default:
        break;
    }
    // for multiple-key combos
    this.n_PrevKeypress = event.which;
  }

  /**
   * Processes the current command
   */
  this.f_ProcessCommand = function ()
  {
    // some commands are handled locally
    switch (this.s_CurrentCommand)
    {
      case 'history':
        this.f_PrintHistory();
      case 'exit':
        //@todo this requires hiding the console parent. It would be nice to cause a chrome-appified WACC close on 'exit'
        break;
      case 'clear':
      case 'cls':
        this.o_HistoryContainer.children(':visible').hide();
        break;
      default:
        if (this.s_CurrentCommand.length > 0)// because people like hitting enter
        {
          // away we go
          s_RequestPath = this.s_ServerPath + '/' + this.s_SID + '/' + encodeURIComponent(this.s_CurrentCommand);
          $.getJSON(s_RequestPath).success(
            function (a_ResponseData)
            {
              self.f_ProcessResponse(a_ResponseData);
            }
          ).error(
            function (o_XHR)
            {
              self.f_ProcessResponseError(o_XHR);
            }
          );
        }
        break;
    }
  }

  /**
   * Processes a successful AJAX command response
   */
  this.f_ProcessResponse = function (a_ResponseData)
  {
    o_ResponseDiv = $('<div/>');

    switch (a_ResponseData['code'])
    {
      case 0:
        break;
      default:
        o_ResponseDiv.addClass('.error');
        break;
    }

    o_ResponseDiv.html(a_ResponseData['output']);

    this.o_HistoryContainer.append(o_ResponseDiv);

    this.f_ScrollConsole();
  }


  /**
   * Processes an unsuccessful AJAX command response
   */
  this.f_ProcessResponseError = function (o_XHR)
  {
    o_ResponseDiv = $('<div/>', {class: 'error'});

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

    this.f_ScrollConsole();
  }


  /**
   * Performs necessary functions to the prompt after a command is entered
   */
  this.f_ShufflePrompt = function ()
  {
    // keep history from getting polluted with a bunch of blank lines
    if (this.s_CurrentCommand.length > 0) this.a_History.push(this.s_CurrentCommand);

     // move the prompt command to the history list
    this.o_HistoryContainer.append($('<div/>').text(this.s_PromptText + ' ' + this.s_CurrentCommand));

    // blank the prompt
    this.o_CommandLine.val('');

    this.f_ScrollConsole();
  }


  /**
   * Keeps the console scrolled to the bottom. Must be called whenever anything is written into the console
   *
   * @todo see if it is possible to bind this function to a 'just got a new child' event of o_HistoryContainer
   */
  this.f_ScrollConsole = function ()
  {
    // the [0] accesses the base DOM element
    this.o_Parent.scrollTop(this.o_Console[0].scrollHeight);
  }


  //
  // History Functions
  //


  /**
   * Moves the history pointer to the correct position when a command is sent
   */
  this.f_ShuffleHistory = function ()
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
  }


  /**
   * Recalls the last command in the command history and displays it in the prompt
   */
  this.f_HistoryPrevious = function ()
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

      if (this.i_HistoryPointer > 0) this.i_HistoryPointer--;
    }
  }


  /**
   * Recalls the next command in the command history and displays it in the prompt
   */
  this.f_HistoryNext = function ()
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
   * Prints the entire command history
   */
  this.f_PrintHistory = function ()
  {
    for (index in this.a_History)
    {
      this.o_HistoryContainer.append($('<div/>').text(this.a_History[index]));
    }
  }
}
