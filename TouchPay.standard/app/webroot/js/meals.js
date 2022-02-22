window.addEventListener('DOMContentLoaded', start_meals);

function start_meals(reload) {

  let startTime = Date.now();
  let suspendMins = 60;

  const HIDDEN_MEAL_ID = "hidden-meal";
  const MEALS_ID = "meals";
  const DATA_ID = "data-id";
  const BASE_URI = location.pathname.split("/")[1];
  const DEFAULT_INTERVAL_MSEC = 5000;
  const NEW_MEAL_CLASS = "new-meal";
  const REMOVED_MEAL_CLASS = "removed-meal";
  const RELOAD_BUTTON_ID = "reload-button";
  const NEXT_UPDATE_MESSAGE = "nextUpdateMessage";
  const LAST_UPDATED = "lastUpdated";

  if (!document.getElementById(HIDDEN_MEAL_ID) || !document.getElementById(MEALS_ID)) {
    return;
  }
  if (document.getElementById(RELOAD_BUTTON_ID)) {
    document.getElementById(RELOAD_BUTTON_ID).style.display = 'none';
  }  

  function updatePage(json, id) {
    if (validateJson(json)) {
      const response = JSON.parse(json);
      const lastUpdated = updateLastUpdated(response.lastUpdated, response.interval);
      const newID = prependMeals(response.limit, response.newlyArrived, response.cancelled);
      const interval = response.interval ? parseInt(response.interval) : DEFAULT_INTERVAL_MSEC;
      if (response.suspend && parseInt(response.suspend) != suspendMins) {
        suspendMins = parseInt(response.suspend);
         startTime = Date.now();
      }
      if (Date.now() - startTime < (suspendMins * 60000)) {
        setTimeout(start.bind(null, (newID || id), lastUpdated), interval);
      } else {
        document.getElementById(RELOAD_BUTTON_ID) && (document.getElementById(RELOAD_BUTTON_ID).style.display = '');
        document.getElementById(NEXT_UPDATE_MESSAGE) && (domInterval.textContent = '画面を更新したい場合には、');
      }
    }
  }

  function validateJson(json) {
    if (!json) {
      return false;
    }
    try {
      const response = JSON.parse(json);
      if (!response) {
        return false;
      }
      console.log(JSON.stringify(response));
    } catch (e) {
      return false;
    }
    return true;
  }

  function updateLastUpdated(lastUpdated, interval) {
    const domLastUpdated = document.getElementById(LAST_UPDATED);
    domLastUpdated && (domLastUpdated.textContent = formatDate(lastUpdated));
    const domInterval = document.getElementById(NEXT_UPDATE_MESSAGE);
    domInterval && (domInterval.textContent = `次の更新は${parseInt(interval)/1000}秒後`);
    return lastUpdated;
  }

  function formatDate(date) {
    try {
      return [
        [date.substring(0, 4), date.substring(4, 6), date.substring(6, 8)].join("/"),
        [date.substring(8, 10), date.substring(10, 12), date.substring(12, 14)].join(":"),
      ].join(" ");
    } catch (e) {
    }
    return "";
  }

  function prependMeals(limit, newlyArrived, cancelled) {
    const meals = document.getElementById(MEALS_ID);
    if (!meals) {
      return;
    }
    updateMeals(meals, (newlyArrived && newlyArrived.length > 0), cancelled);
    let id = null;
    newlyArrived.forEach(function(e) {
      id = e.FoodHistoryInfo.id;
      let mealNode = newMeal(id);
      ["employee_id", "employee_name1", "employee_name2", "card_recept_time", "employee_kbn_name", "instrument_name", "food_division_name", "food_cost"].forEach(function(name){
        let node = mealNode.querySelector("." + name);
        if (node) {
          [e.FoodHistoryInfo, e.EmployeeInfo, e.EmployeeKbn, e.InstrumentDivision, e.FoodDivision].every(function(obj) {
            if (obj && obj[name]) {
               node.textContent = obj[name];
               return false;
            }
            return true;
          });
        }
      });
      prependMeal(meals, mealNode, limit);
    });
    return id;
  }

  function updateMeals(meals, newlyArrived, cancelled) {
    Array.from(meals.children).reverse().forEach(function(meal){
      if (newlyArrived && meal.classList.contains(NEW_MEAL_CLASS)) {
        meal.classList.remove(NEW_MEAL_CLASS);
      }
      let value = meal.getAttribute(DATA_ID);
      if (value && cancelled && cancelled[value]) {
        meal.classList.add(REMOVED_MEAL_CLASS);
      }
    });
  }

  function prependMeal(meals, newMeal, limit) {
    meals.firstChild && meals.insertBefore(newMeal, meals.firstChild) || meals.appendChild(newMeal);
    while (meals.children.length > parseInt(limit)) {
      meals.removeChild(meals.lastChild);
    }
  }

  function newMeal(id) {
    const newMeal = document.getElementById(HIDDEN_MEAL_ID).cloneNode(true);
    newMeal.style.display = '';
    newMeal.removeAttribute('id');
    newMeal.setAttribute(DATA_ID, id);
    newMeal.classList.add(NEW_MEAL_CLASS);
    return newMeal;
  }

  function start(id, lastUpdated) {
    const request = new XMLHttpRequest();
    request.onreadystatechange = function() {
      if (request.readyState === 4) {
        if (request.status === 200) {
          updatePage(request.responseText, id);
        } else if (request.status >= 500) {
          setTimeout(start.bind(null, id, lastUpdated), DEFAULT_INTERVAL_MSEC);
        }
      }
    };
    let url = `/${BASE_URI}/json/meals`;
    if (id) {
      url = `${url}/${encodeURIComponent(id)}`;
      if (lastUpdated) {
        url = `${url}/${encodeURIComponent(lastUpdated)}`;
      }
    }
    request.open("POST", url, true);
    request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;charset=UTF-8");
    request.send();
  }

  start();
}