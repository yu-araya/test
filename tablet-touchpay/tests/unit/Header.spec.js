import { shallowMount } from "@vue/test-utils"
import Header from "@/components/order/Header.vue"
import Vue from "vue"

Vue.config.ignoredElements = ["b-icon", "b-modal", "b-navbar", "b-navbar-item"]

let wrapper;

beforeEach(() => {
  wrapper = shallowMount(Header)
})

afterEach(() => {
  wrapper.destroy();
})

describe("Header.vue", () => {
  it("iconが表示されていること　ロゴ　　　（ログ送信）リロード", () => {
    const wrapper = shallowMount(Header)
    const images = wrapper.findAll("img").wrappers
    images.forEach((img, index) => {
      switch (index) {
        case 0:
          expect(img.attributes().id).toBe('logo')
          break
        case 1:
          expect(img.attributes().id).toBe('reload')
          break
      }
    })
  })

  it("version", () => {
    expect(wrapper.text()).toBe('注文画面 v1.0.0 未送信件数: 0件')
  })

  it('売り切れ設定ボタンを押したらopenSoldOutConfが呼び出される', () => {
    const openSoldOutConf = jest.fn()
    wrapper.setMethods({ openSoldOutConf })
    wrapper.find('#soldout').trigger('click')
    expect(openSoldOutConf).toBeCalled()
  })

  it('売り切れ設定ボタンを押したらisSoldOutModalActiveがtrueになる', () => {
    expect(wrapper.vm.isSoldOutModalActive).toBeFalsy()
    wrapper.find('#soldout').trigger('click')
    expect(wrapper.vm.isSoldOutModalActive).toBeTruthy()
  })

  it('リロードボタンを押したらreloadメソッドが呼び出される', () => {
    const reload = jest.fn()
    wrapper.setMethods({ reload })
    wrapper.find('#reload').trigger('click')
    expect(reload).toBeCalled()
  })
})