/*
 * ピットタッチ・プロ サンプルコンテンツ
 * ProOperate Wrapper
 * ProOperateをPCで動作させるためのラッパークラス
 * ReferenceErrorが発生するので、代わりの値をで決めている
 * 
 * COPYRIGHT (C) 2011 B.U.G., INC.  ALL RIGHTS RESERVED.
 * 
 * @author B.U.G., INC.
 * @version 1.0
 * 
 * Licensed under the MIT License: http://www.opensource.org/licenses/mit-license.php
 * 
 */

function ProOperateWrapper()
{	
}

ProOperateWrapper.prototype.getFirmwareVersion = function()
{
	var result = "1.00";
	
	try
	{
		var op = new ProOperate();
		result =  op.getFirmwareVersion();
	}
	catch (e)
	{
		if (e.name == "ReferenceError")
		{
			console.info("ReferenceError:getFirmwareVersion");
		}
		else
			throw e;
	}
	
	return result;
};

ProOperateWrapper.prototype.getNetworkStat = function()
{
	var result = 2;
	
	try
	{
		var op = new ProOperate();
		result =  op.getNetworkStat();
	}
	catch (e)
	{
		if (e.name == "ReferenceError")
		{
			console.info("ReferenceError:getFirmwareVersion");
		}
		else
			throw e;
	}
	
	return result;
};

ProOperateWrapper.prototype.getKeypadConnected = function()
{
	var result = 1;
	
	try
	{
		var op = new ProOperate();
		result =  op.getKeypadConnected();
	}
	catch (e)
	{
		if (e.name == "ReferenceError")
		{
			console.info("ReferenceError:getKeypadConnected");
		}
		else
			throw e;
	}
	
	return result;
};

ProOperateWrapper.prototype.startEventListen = function(param)
{
	var result = 0;
	
	try
	{
		var op = new ProOperate();
		result =  op.startEventListen(param);
	}
	catch (e)
	{
		if (e.name == "ReferenceError")
		{
			console.info("ReferenceError:startEventListen");
		}
		else
			throw e;
	}
	
	return result;
};


ProOperateWrapper.prototype.getKeypadDisplay = function()
{
	var result = {};
	result.str1 = "A";
	result.str2 = "B";
	
	try
	{
		var op = new ProOperate();
		result =  op.getKeypadDisplay();
	}
	catch (e)
	{
		if (e.name == "ReferenceError")
		{
			console.info("ReferenceError:getKeypadDisplay");
		}
		else
			throw e;
	}
	
	return result;
};


ProOperateWrapper.prototype.getKeypadLed = function()
{
	var result = "664455113322664455113322";
	
	try
	{
		var op = new ProOperate();
		result =  op.getKeypadLed();
	}
	catch (e)
	{
		if (e.name == "ReferenceError")
		{
			console.info("ReferenceError:getKeypadLed");
		}
		else
			throw e;
	}
	
	return result;
};

ProOperateWrapper.prototype.getSequentialID = function()
{
	var result = 1;
	
	try
	{
		var op = new ProOperate();
		result =  op.getSequentialID();
	}
	catch (e)
	{
		if (e.name == "ReferenceError")
		{
			console.info("ReferenceError:getSequentialID");
		}
		else
			throw e;
	}
	
	return result;
};

ProOperateWrapper.prototype.getTerminalID = function()
{
	var result = "08443127";
	
	try
	{
		var op = new ProOperate();
		result =  op.getTerminalID();
	}
	catch (e)
	{
		if (e.name == "ReferenceError")
		{
			console.info("ReferenceError:getTerminalID");
		}
		else
			throw e;
	}
	
	return result;
};

ProOperateWrapper.prototype.playSound = function(param)
{
	var result = 1;
	
	try
	{
		var op = new ProOperate();
		result =  op.playSound(param);
	}
	catch (e)
	{
		if (e.name == "ReferenceError")
		{
			console.info("ReferenceError:playSound");
		}
		else
			throw e;
	}
	
	return result;
};

ProOperateWrapper.prototype.reboot = function()
{
	try
	{
		var op = new ProOperate();
		op.reboot();
	}
	catch (e)
	{
		if (e.name == "ReferenceError")
		{
			console.info("ReferenceError:reboot");
		}
		else
			throw e;
	}
};

ProOperateWrapper.prototype.removeAllWebSQLDB = function()
{
	try
	{
		var op = new ProOperate();
		op.removeAllWebSQLDB();
	}
	catch (e)
	{
		if (e.name == "ReferenceError")
		{
			console.info("ReferenceError:removeAllWebSQLDB");
		}
		else
			throw e;
	}
};

ProOperateWrapper.prototype.setDate = function(year, month, day, hour, minute, second)
{
	var result = 0;
	
	try
	{
		var op = new ProOperate();
		result =  op.setDate(year, month, day, hour, minute, second);
	}
	catch (e)
	{
		if (e.name == "ReferenceError")
		{
			console.info("ReferenceError:setDate:" + year + "/" + month + "/" + day + " " + hour + ":"+ minute + ":" + second);
		}
		else
			throw e;
	}
	
	return result;
};

ProOperateWrapper.prototype.setKeypadDisplay = function(param)
{
	var result = 0;
	
	try
	{
		var op = new ProOperate();
		result =  op.setKeypadDisplay(param);
	}
	catch (e)
	{
		if (e.name == "ReferenceError")
		{
			console.info("ReferenceError:setKeypadDisplay");
		}
		else
			throw e;
	}
	
	return result;
};

ProOperateWrapper.prototype.setKeypadLed = function(param)
{
	var result = 0;
	
	try
	{
		var op = new ProOperate();
		result =  op.setKeypadLed(param);
	}
	catch (e)
	{
		if (e.name == "ReferenceError")
		{
			console.info("ReferenceError:setKeypadLed");
		}
		else
			throw e;
	}
	
	return result;
};

ProOperateWrapper.prototype.shutdown = function()
{
	try
	{
		var op = new ProOperate();
		op.shutdown();
	}
	catch (e)
	{
		if (e.name == "ReferenceError")
		{
			console.info("ReferenceError:shutdown");
		}
		else
			throw e;
	}
};

ProOperateWrapper.prototype.startCommunication = function(param)
{
	var result = 0;
	
	try
	{
		var op = new ProOperate();
		result =  op.startCommunication(param);
	}
	catch (e)
	{
		if (e.name == "ReferenceError")
		{
			console.info("ReferenceError:startCommunication");
		}
		else
			throw e;
	}
	
	return result;
};

ProOperateWrapper.prototype.startKeypadListen = function(param)
{
	var result = 0;
	
	try
	{
		var op = new ProOperate();
		result =  op.startKeypadListen(param);
	}
	catch (e)
	{
		if (e.name == "ReferenceError")
		{
			console.info("ReferenceError:startKeypadListen");
		}
		else
			throw e;
	}
	
	return result;
};

ProOperateWrapper.prototype.stopCommunication = function()
{
	var result = 0;
	
	try
	{
		var op = new ProOperate();
		result =  op.stopCommunication();
	}
	catch (e)
	{
		if (e.name == "ReferenceError")
		{
			console.info("ReferenceError:stopCommunication");
		}
		else
			throw e;
	}
	
	return result;
};

ProOperateWrapper.prototype.stopEventListen = function()
{
	var result = 0;
	
	try
	{
		var op = new ProOperate();
		result =  op.stopEventListen();
	}
	catch (e)
	{
		if (e.name == "ReferenceError")
		{
			console.info("ReferenceError:stopEventListen");
		}
		else
			throw e;
	}
	
	return result;
};

ProOperateWrapper.prototype.stopKeypadListen = function()
{
	var result = 0;
	
	try
	{
		var op = new ProOperate();
		result =  op.stopKeypadListen();
	}
	catch (e)
	{
		if (e.name == "ReferenceError")
		{
			console.info("ReferenceError:stopKeypadListen");
		}
		else
			throw e;
	}
	
	return result;
};

ProOperateWrapper.prototype.stopSound = function(param)
{
	var result = 0;
	
	try
	{
		var op = new ProOperate();
		result =  op.stopSound(param);
	}
	catch (e)
	{
		if (e.name == "ReferenceError")
		{
			console.info("ReferenceError:stopSound");
		}
		else
			throw e;
	}
	
	return result;
};

