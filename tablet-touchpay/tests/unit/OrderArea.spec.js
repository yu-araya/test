import { shallowMount } from "@vue/test-utils"
import OrderArea from "@/components/order/OrderArea.vue"
import Vue from "vue"
import { state } from "@/store/store"

let wrapper;

const zeroMenuLists = [{
  category: "1",
  food_division: 1,
  food_division_name: "牛丼",
  food_cost: 100,
  count: 0,
  dispCard: true,
}]

const menuLists = [{
  category: "1",
  food_division: 1,
  food_division_name: "牛丼",
  food_cost: 100,
  count: 2,
  dispCard: true,
}, {
  category: "2",
  food_division: 2,
  food_division_name: "カレー",
  food_cost: 200,
  count: 0,
  dispCard: false,
}, {
  category: "2",
  food_division: 3,
  food_division_name: "ハンバーガー",
  food_cost: 150,
  count: 4,
  dispCard: true,
}]

beforeEach(() => {
  wrapper = shallowMount(OrderArea)
})

afterEach(() => {
  wrapper.destroy();
})

describe("OrderArea.vue", () => {
  // it("keypressイベントを検知してidmInputEventが呼び出される", () => {
  //   const idmInputEvent = jest.fn()
  //   wrapper.setMethods({ idmInputEvent })
  //   wrapper.trigger('keydown', {
  //     key: 'a'
  //   })
  //   expect(idmInputEvent).toBeCalled()
  // })

  it("注文が無い場合createMessageが呼び出される", () => {
    state.menuLists = zeroMenuLists
    const createMessage = jest.fn()
    wrapper.setMethods({ createMessage })
    wrapper.vm.idmInputEvent({ key: "a", which: 1 })
    expect(createMessage).toBeCalled()
  })

  it("Enter以外が入力されるとkeyに値が追加される", () => {
    state.menuLists = menuLists
    wrapper.vm.idmInputEvent({ key: "a", which: 1 })
    expect(wrapper.vm.key).toBe("a")
  })

  // it("Enterが入力されるとsubmitが実行される", () => {
  //   const submit = jest.fn()
  //   wrapper.setMethods = ({ submit })
  //   wrapper.vm.idmInputEvent({ key: "Enter", which: 13 })
  //   expect(submit).toBeCalled()
  // })

  it("getOrderListsを呼び出すと注文状況の配列が返却される", async () => {
    const exp = [{
      foodDivision: 1,
      count: 2,
      foodCost: 100
    }, {
      foodDivision: 3,
      count: 4,
      foodCost: 150
    }]

    expect(await wrapper.vm.getOrderLists(menuLists)).toEqual(exp)
  })

  it("createRegistRequestDataを呼び出すと喫食状況のオブジェクトが返却される", async () => {
    const employeeId = '7777'
    const date = new Date()
    date.setTime(date.getTime() + 1000 * 60 * 60 * 9);
    const exp = {
      employeeId: employeeId,
      instrumentDivision: "1",
      order: [{
        foodDivision: 1,
        count: 2,
        foodCost: 100
      }, {
        foodDivision: 3,
        count: 4,
        foodCost: 150
      }],
      cardReceptDateTime: date
    }
    expect(await wrapper.vm.createRegistRequestData(employeeId)).toEqual(exp)
  })
})