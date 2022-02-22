import { shallowMount } from "@vue/test-utils"
import MenuCard from "@/components/order/MenuCard.vue"
import { state } from "@/store/store"
import { MenuData } from '@/store/types';
import { maxOrderNum } from "@/assets/js/constant"

let wrapper;

const menuData = {
  category: "1",
  food_division: 1,
  food_division_name: "牛丼",
  food_cost: 1200,
  count: 0,
  dispCard: true,
}

beforeEach(() => {
  state.menuLists.push(menuData)
  wrapper = shallowMount(MenuCard, {
    propsData: { menuData: menuData }
  })
})

afterEach(() => {
  wrapper.destroy();
})

describe("MenuCard.vue", () => {
  // it("menuDataを受け取らなかった場合エラーになる", () => {
  //   const noPropWrapper = shallowMount(MenuCard)
  //   expect(noPropWrapper).toThrowError()
  // })

  it("propで受け取ったメニュー名と金額を表示する、カンマ編集済", () => {
    const exp = "牛丼 ¥1,200"
    expect(wrapper.text()).toBe(exp)
  })

  it("ボタンクリックでclickメソッド呼び出し", () => {
    const click = jest.fn()
    wrapper.setMethods({ click })
    wrapper.find('button').trigger('click')
    expect(click).toBeCalled()
  })

  it("タップしたらplaySoundが呼び出される、MenuDataのカウントが増える、createMessageは呼び出されない", () => {
    const playSound = jest.fn()
    const createMessage = jest.fn()
    wrapper.setMethods({ playSound, createMessage })
    wrapper.vm.click()
    const props = wrapper.props()
    expect(playSound).toBeCalled()
    expect(props.menuData.count).toBe(1)
    expect(createMessage).toHaveBeenCalledTimes(0)
  })

  it("カウントがconstantで定められている最大値の状態で再びタップされるとcreateMessageメソッドが呼び出される", () => {
    const playSound = jest.fn()
    const createMessage = jest.fn()
    wrapper.setMethods({ playSound, createMessage })
    for (let i = wrapper.props().menuData.count; i <= maxOrderNum; i++) {
      wrapper.vm.click()
    }
    const props = wrapper.props()
    expect(props.menuData.count).toBe(maxOrderNum)
    expect(createMessage).toHaveBeenCalledTimes(1)
  })
})