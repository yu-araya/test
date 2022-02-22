import { shallowMount } from "@vue/test-utils"
import { state, mutations } from "@/store/store"
import SoldOut from "@/components/order/SoldOut.vue"
import Vue from "vue"
import { SoldOutInfos } from "@/assets/js/indexedDB"

Vue.config.ignoredElements = ["b-checkbox-button", "b-button"]

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

const soldoutList = [1, 3]

const soldOutInfos = new SoldOutInfos()

describe("SoldOut.vue", () => {
  let wrapper;

  beforeEach(() => {
    state.menuLists = menuLists
    state.soldoutList = soldoutList
    wrapper = shallowMount(SoldOut, {
      propsData: { isSoldOutModalActive: true }
    })
  })

  afterEach(() => {
    wrapper.destroy();
  })

  it("ローカルのsoldoutListがstoreと等しいこと", () => {
    expect(wrapper.vm.soldoutList).toBe(soldoutList)
  })

  it("メニューが３つ表示されていること", () => {
    const menues = wrapper.findAll(".soldout__button").wrappers
    menues.forEach((menu, index) => {
      expect(menu.text()).toBe(menuLists[index].food_division_name)
    })
    const save = wrapper.find(".soldout__save")
    expect(save.text()).toBe("保存")
  })

  it("保存ボタン押下でsaveメソッド呼び出し", () => {
    const save = jest.fn()
    wrapper.setMethods({ save })
    wrapper.find(".soldout__save").trigger("click")
    expect(save).toBeCalled()
  })

  it("saveメソッド実行でsoldOutInfos.inserSoldOutData実行", async () => {
    const insertSoldoutData = jest.fn()
    wrapper.vm.soldOutInfos.insertSoldoutData = insertSoldoutData
    // const initialize = jest.fn()
    // mutations.initialize = initialize
    wrapper.vm.save()
    expect(insertSoldoutData).toBeCalled()
    // expect(initialize).toBeCalled()
  })
})